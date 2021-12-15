<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Test\Integration\Model\DataLayer;

/**
 * @magentoAppArea frontend
 */
class ProductChildrenProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \MagePal\GoogleTagManager\DataLayer\ProductData\ProductProvider
     */
    protected $productDataProvider;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->productDataProvider = $objectManager->get(\MagePal\GoogleTagManager\DataLayer\ProductData\ProductProvider::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testItReturnProductDataCorrectly()
    {
        $product = $this->productRepository->get('simple');
        $productData = $this->productDataProvider->setProduct($product)->getData();

        $this->assertEmpty($productData['productIds']);
        $this->assertEquals(
            \MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\DataProviderInterface::PRODUCT_SEPARATOR,
            $productData['productSeparator']
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testItReturnConfigurableProductDataCorrectly()
    {
        $product = $this->productRepository->get('configurable');
        $productData = $this->productDataProvider->setProduct($product)->getData();

        $this->assertEquals('10,20', $productData['productIds']);
        $this->assertEquals(
            \MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\DataProviderInterface::PRODUCT_SEPARATOR,
            $productData['productSeparator']
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Bundle/_files/product_with_multiple_options.php
     */
    public function testItReturnBundleProductDataCorrectly()
    {
        $product = $this->productRepository->get('bundle-product');
        $productData = $this->productDataProvider->setProduct($product)->getData();

        $this->assertEquals('10,11', $productData['productIds']);
        $this->assertEquals(
            \MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\DataProviderInterface::PRODUCT_SEPARATOR,
            $productData['productSeparator']
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/GroupedProduct/_files/product_grouped.php
     */
    public function testItReturnGroupedProductDataCorrectly()
    {
        $product = $this->productRepository->get('grouped-product');
        $productData = $this->productDataProvider->setProduct($product)->getData();

        $this->assertEquals('1,21', $productData['productIds']);
        $this->assertEquals(
            \MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\DataProviderInterface::PRODUCT_SEPARATOR,
            $productData['productSeparator']
        );
    }
}
