<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Observer;

class DecreaseNumberOfOrders implements \Magento\Framework\Event\ObserverInterface
{
    protected \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository;

    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getOrder();
        $customerId = $order->getCustomerId();

        if ($customerId === null) {
            return;
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
            $attributeValue = $customer->getCustomAttribute('number_of_orders');

            if ($attributeValue instanceof \Magento\Framework\Api\AttributeInterface) {
                $value = (int)$attributeValue->getValue() - 1;
            } else {
                $value = 0;
            }

            $customer->setCustomAttribute('number_of_orders', $value);
            $this->customerRepository->save($customer);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
