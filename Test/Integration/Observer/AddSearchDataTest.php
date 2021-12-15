<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Test\Integration\Observer;

class AddSearchDataTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serializer = $this->_objectManager->get(\Magento\Framework\Serialize\SerializerInterface::class);
        $this->indexerRegistry = $this->_objectManager->get(\Magento\Framework\Indexer\IndexerRegistry::class);
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture Magento/Catalog/_files/category_product.php
     * @magentoConfigFixture current_store googletagmanager/general/active 1
     * @magentoConfigFixture current_store googletagmanager/general/account GTM-XXXXXX
     */
    public function testProductDataOnSearchResultPage(): void
    {
        $indexer = $this->indexerRegistry->get(\Smile\ElasticsuiteCms\Model\Page\Indexer\Fulltext::INDEXER_ID);

        if ($indexer->isInValid()) {
            $indexer->reindexAll();
        }

        $this->getRequest()->setParam('q', 'simple');
        $this->dispatch('/catalogsearch/result/index');
        $body = $this->getResponse()->getBody();
        $expectedString = $this->serializer->serialize([
           'search' => [
               'queryText' => 'simple',
               'productIds' => '333',
               'productSeparator' => \MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\DataProviderInterface::PRODUCT_SEPARATOR
           ],
           'event' => 'searchPage'
        ]);

        $this->assertStringContainsString($expectedString, $body);
    }
}
