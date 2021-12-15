<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Test\Integration\Model\DataLayer;

/**
 * @magentoAppArea frontend
 */
class OrderDataProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \MagePal\GoogleTagManager\DataLayer\OrderData\OrderProvider
     */
    protected $orderDataProvider;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->order = $objectManager->create(\Magento\Sales\Model\Order::class);
        $this->orderDataProvider = $objectManager->get(\MagePal\GoogleTagManager\DataLayer\OrderData\OrderProvider::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Sales/_files/order_with_two_simple_products.php
     */
    public function testItReturnProductDataCorrectly()
    {
        $order = $this->order->loadByIncrementId('100000001');
        $orderData = $this->orderDataProvider->setOrder($order)->getData();

        $this->assertEquals('1,22', $orderData['productIds']);
        $this->assertEquals(
            \MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\DataProviderInterface::PRODUCT_SEPARATOR,
            $orderData['productSeparator']
        );
    }
}
