<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Observer;

class AddCategoryData implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MagePal\GoogleTagManager\DataLayer\CategoryData\CategoryProvider
     */
    protected $categoryProvider;

    /**
     * @param \MagePal\GoogleTagManager\DataLayer\CategoryData\CategoryProvider $categoryProvider
     */
    public function __construct(\MagePal\GoogleTagManager\DataLayer\CategoryData\CategoryProvider $categoryProvider)
    {
        $this->categoryProvider = $categoryProvider;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        /** @var \MagePal\GoogleTagManager\Block\DataLayer $dataLayer **/
        $dataLayer = $observer->getData('dataLayer');
        $category = $this->categoryProvider->getCategory();

        if (!$category instanceof \Magento\Catalog\Model\Category) {
            return;
        }

        $categoryData = $this->categoryProvider->getData();
        $data = ['category' => $categoryData];
        $dataLayer->addCustomDataLayerByEvent(
            \MagePal\GoogleTagManager\Model\DataLayerEvent::CATEGORY_PAGE_EVENT,
            $data
        );
    }
}
