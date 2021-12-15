<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer;

class CategoryDataProvider extends \MagePal\GoogleTagManager\DataLayer\CategoryData\CategoryAbstract implements DataProviderInterface
{
    /**
     * @var array
     */
    protected $productIds = [];

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
     * @param array $productIds
     * @return $this
     */
    public function setProductIds(array $productIds): self
    {
        $this->productIds = $productIds;
        return $this;
    }

    /**
     * @return array
     */
    protected function getProductIds(): array
    {
        return $this->productIds;
    }
}
