<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Plugin\Catalog\Block\Product\ListProduct;

class SetProductIdsForProductListProvider
{
    const LOADED_PRODUCT_IDS_FLAG = 'loaded_product_ids_flag';

    /**
     * @var \MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\CategoryDataProvider
     */
    protected $categoryDataProvider;

    public function __construct(\MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\CategoryDataProvider $categoryDataProvider)
    {
        $this->categoryDataProvider = $categoryDataProvider;
    }

    public function afterGetLoadedProductCollection(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        $result
    ) {
        if ($subject->getData(self::LOADED_PRODUCT_IDS_FLAG) || !$result->count()) {
            return $result;
        }

        $productIds = $result->getColumnValues('entity_id');
        $this->categoryDataProvider->setProductIds($productIds);
        $subject->setData(self::LOADED_PRODUCT_IDS_FLAG, true);

        return $result;
    }
}
