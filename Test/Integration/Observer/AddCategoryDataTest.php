<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Test\Integration\Observer;

class AddCategoryDataTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;
    protected function setUp(): void
    {
        parent::setUp();
        $this->serializer = $this->_objectManager->get(\Magento\Framework\Serialize\SerializerInterface::class);
        $this->productMetadata = $this->_objectManager->get(\Magento\Framework\App\ProductMetadataInterface::class);
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture Magento/Catalog/_files/category_product.php
     * @magentoConfigFixture current_store googletagmanager/general/active 1
     * @magentoConfigFixture current_store googletagmanager/general/account GTM-XXXXXX
     */
    public function testProductDataOnCategoryPage(): void
    {
        $this->getRequest()->setParam('id', 333);
        $this->dispatch('/catalog/category/view');
        $body = $this->getResponse()->getBody();

        $expectedString = $this->serializer->serialize([
            'event' => 'categoryPage',
            'category' => [
                'id' => '333',
                'name' => 'Category 1',
                'path' => '',
                'productIds' => '333',
                'productSeparator' => \MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\DataProviderInterface::PRODUCT_SEPARATOR
            ]
        ]);

        if (version_compare($this->productMetadata->getVersion(), '2.4.6') >= 0) {
            $expectedString = $this->serializer->serialize([
                'event' => 'categoryPage',
                'category' => [
                    'id' => '333',
                    'name' => 'Category 1',
                    'path' => 'Category 1',
                    'productIds' => '333',
                    'productSeparator' => \MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\DataProviderInterface::PRODUCT_SEPARATOR
                ]
            ]);
        }

        $this->assertStringContainsString($expectedString, $body);
    }
}
