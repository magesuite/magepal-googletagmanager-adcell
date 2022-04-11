<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer;

class OrderDataProvider extends \MagePal\GoogleTagManager\DataLayer\OrderData\OrderAbstract implements DataProviderInterface
{
    protected \Magento\Customer\Model\Session $customerSession;

    protected \Magento\Sales\Model\ResourceModel\Order $orderResource;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\ResourceModel\Order $orderResource
    ) {
        $this->customerSession = $customerSession;
        $this->orderResource = $orderResource;
    }

    public function getData(): array
    {
        $productIds = $this->getProductIds();
        $data = [
            'productIds' => implode(self::PRODUCT_SEPARATOR, $productIds),
            'productSeparator' => self::PRODUCT_SEPARATOR
        ];
        $customerId = (int)$this->getOrder()->getCustomerId();

        if ($customerId) {
            $data['customerState'] = $this->getCustomerState();
        }

        return $data;
    }

    protected function getProductIds(): array
    {
        $productIds = [];

        foreach ($this->getOrder()->getAllVisibleItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        return $productIds;
    }

    protected function getCustomerState(): int
    {
        $customer = $this->customerSession->getCustomer();
        $numberOfOrders = $customer->getData('number_of_orders');

        return $numberOfOrders > 0 ? 1 : 0;
    }
}
