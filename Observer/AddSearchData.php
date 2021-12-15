<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Observer;

class AddSearchData implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\SearchDataProvider
     */
    protected $searchDataProvider;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer\SearchDataProvider $searchDataProvider
    ) {
        $this->request = $request;
        $this->searchDataProvider = $searchDataProvider;
    }

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        if ($this->request->getFullActionName() !== 'catalogsearch_result_index') {
            return;
        }

        /** @var \MagePal\GoogleTagManager\Block\DataLayer $dataLayer **/
        $dataLayer = $observer->getData('dataLayer');
        $searchData = $this->searchDataProvider->getData();
        $data = ['search' => $searchData];
        $dataLayer->addCustomDataLayerByEvent(
            \MagePal\GoogleTagManager\Model\DataLayerEvent::SEARCH_PAGE_EVENT,
            $data
        );
    }
}
