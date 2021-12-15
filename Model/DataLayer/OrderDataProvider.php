<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer;

class OrderDataProvider extends \MagePal\GoogleTagManager\DataLayer\OrderData\OrderAbstract implements DataProviderInterface
{
    /**
     * @return array
     */
    public function getData(): array
    {
        $productIds = $this->getProductIds();

        return [
            'productIds' => implode(self::PRODUCT_SEPARATOR, $productIds),
            'productSeparator' => self::PRODUCT_SEPARATOR
        ];
    }

    /**
     * @return array
     */
    protected function getProductIds(): array
    {
        $productIds = [];

        foreach ($this->getOrder()->getAllVisibleItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        return $productIds;
    }
}
