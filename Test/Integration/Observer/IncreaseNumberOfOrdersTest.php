<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Test\Integration\Observer;

class IncreaseNumberOfOrdersTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    protected $quoteManagement;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->searchCriteriaBuilder = $objectManager->get(
            \Magento\Framework\Api\SearchCriteriaBuilder::class
        );
        $this->cartRepository = $objectManager->get(
            \Magento\Quote\Api\CartRepositoryInterface::class
        );
        $this->quoteManagement = $objectManager->get(
            \Magento\Quote\Model\QuoteManagement::class
        );
        $this->customerRepository = $objectManager->get(
            \Magento\Customer\Api\CustomerRepositoryInterface::class
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Sales/_files/quote_with_customer.php
     */
    public function testItAddsAndRemoveNumberOfOrdersToCustomer()
    {
        $quote = $this->getQuote('test01');
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('flatrate_flatrate');
        $quote->getPayment()->importData(['method' => 'checkmo']);
        $quote->collectTotals()->save();

        $order = $this->quoteManagement->submit($quote);
        $this->checkCustomerNumberOfOrders('customer@example.com', 1);
        $order->cancel();
        $this->checkCustomerNumberOfOrders('customer@example.com', 0);
    }

    protected function checkCustomerNumberOfOrders($email, $expectedValue)
    {
        $customer = $this->customerRepository->get($email);
        $attributeValue = $customer->getCustomAttribute('number_of_orders');

        $this->assertNotNull($attributeValue);
        $this->assertEquals($expectedValue, $attributeValue->getValue());
    }

    protected function getQuote($reservedOrderId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('reserved_order_id', $reservedOrderId)
            ->create();
        /** @var \Magento\Quote\Api\CartRepositoryInterface $quoteRepository */
        $items = $this->cartRepository
            ->getList($searchCriteria)
            ->getItems();

        return array_pop($items);
    }
}
