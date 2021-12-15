<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer;

class ProductChildrenDataProvider extends \MagePal\GoogleTagManager\DataLayer\ProductData\ProductAbstract implements DataProviderInterface
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
        $product = $this->getProduct();

        if (!$product->isComposite()) {
            return [];
        }

        $childrenIds = $product->getTypeInstance()->getChildrenIds($product->getId());

        if (empty($childrenIds)) {
            return [];
        }

        return reset($childrenIds);
    }
}
