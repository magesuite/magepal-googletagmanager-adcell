<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Test\Integration\Model\DataLayer;

/**
 * @magentoAppArea frontend
 */
class QuoteDataProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \MagePal\GoogleTagManager\DataLayer\QuoteData\QuoteProvider
     */
    protected $quoteDataProvider;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->quote = $objectManager->create(\Magento\Quote\Model\Quote::class);
        $this->quoteDataProvider = $objectManager->get(\MagePal\GoogleTagManager\DataLayer\QuoteData\QuoteProvider::class);
        $this->searchCriteriaBuilder = $objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $this->quoteRepository = $objectManager->get(\Magento\Quote\Api\CartRepositoryInterface::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Sales/_files/quote_with_multiple_products.php
     */
    public function testItReturnProductDataCorrectly()
    {
        $quote = $this->getQuote('tableRate');
        $orderData = $this->quoteDataProvider->setQuote($quote)->getData();

        $this->assertEquals('123,124,658', $orderData['productIds']);
        $this->assertEquals(
            \MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\DataProviderInterface::PRODUCT_SEPARATOR,
            $orderData['productSeparator']
        );
    }

    protected function getQuote(string $reservedOrderId): \Magento\Quote\Model\Quote
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('reserved_order_id', $reservedOrderId)
            ->create();
        $items = $this->quoteRepository
            ->getList($searchCriteria)
            ->getItems();

        return array_pop($items);
    }
}
