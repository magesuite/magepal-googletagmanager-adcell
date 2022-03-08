<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Plugin\MagePal\GoogleTagManager\CustomerData\JsDataLayer;

class AddRecurringCustomerFlag
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

    public function afterGetSectionData(
        \MagePal\GoogleTagManager\CustomerData\JsDataLayer $subject,
        $result
    ) {
        if (!$this->customerSession->isLoggedIn()) {
            return $result;
        }

        $result['customer']['state'] = $this->getCustomerState();
        return $result;
    }

    protected function getCustomerState(): int
    {
        $customer = $this->customerSession->getCustomer();
        $numberOfOrders = $customer->getData('number_of_orders');

        return $numberOfOrders > 0 ? 1 : 0;
    }
}
