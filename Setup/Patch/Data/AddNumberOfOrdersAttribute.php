<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Setup\Patch\Data;

class AddNumberOfOrdersAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface, \Magento\Framework\Setup\Patch\PatchRevertableInterface
{
    protected \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup;

    protected \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory;

    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'number_of_orders',
            [
                'type' => 'int',
                'label' => 'Number of orders',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'visible' => true,
                'position' => 500,
                'system' => false,
                'backend' => ''
            ]
        );
        $usedInForms = [
            'adminhtml_customer',
            'customer_account_edit',
            'customer_account_create'
        ];
        $attribute = $customerSetup->getEavConfig()
            ->getAttribute('customer', 'number_of_orders')
            ->addData(['used_in_forms' => $usedInForms]);
        $attribute->save();
        $this->migrateCustomerData();
        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    public function revert(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'number_of_orders');
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    protected function migrateCustomerData()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $attributeId = $customerSetup->getAttributeId(
            \Magento\Customer\Model\Customer::ENTITY,
            'number_of_orders'
        );
        $attributeTable = $customerSetup->getAttributeTable(
            \Magento\Customer\Model\Customer::ENTITY,
            'number_of_orders'
        );
        $results = $this->getCustomersAndNumberOfOrders();
        $data = [];
        $count = 0;
        $connection->beginTransaction();

        try {
            foreach ($results as $result) {
                $data[] = [
                    'entity_id' => $result['entity_id'],
                    'attribute_id' => $attributeId,
                    'value' => $result['count']
                ];
                $count++;

                if ($count % 1000 == 0) {
                    $connection->insertMultiple($attributeTable, $data);
                    $data = [];
                }
            }

            if (!empty($data)) {
                $connection->insertMultiple($attributeTable, $data);
            }
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $connection->commit();
    }

    protected function getCustomersAndNumberOfOrders(): array
    {
        $connection = $this->moduleDataSetup->getConnection();
        $select = $connection->select()
            ->from(
                ['main_table' => $connection->getTableName('sales_order_grid')],
                ['count' => new \Zend_Db_Expr('COUNT(*)')]
            )
            ->join(
                ['ce' => $connection->getTableName('customer_entity')],
                'main_table.customer_email = ce.email',
                ['ce.entity_id']
            )
            ->group('main_table.customer_email')
            ->where('main_table.status != ?', \Magento\Sales\Model\Order::STATE_CANCELED);

        return $connection->fetchAll($select);
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [];
    }
}
