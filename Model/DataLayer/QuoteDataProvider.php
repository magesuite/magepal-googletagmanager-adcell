<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer;

class QuoteDataProvider extends \MagePal\GoogleTagManager\DataLayer\QuoteData\QuoteAbstract implements DataProviderInterface
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

        foreach ($this->getQuote()->getAllVisibleItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        return $productIds;
    }
}
