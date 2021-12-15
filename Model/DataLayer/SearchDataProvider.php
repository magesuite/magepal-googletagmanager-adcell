<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer;

class SearchDataProvider implements DataProviderInterface
{
    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $searchHelper;

    /**
     * @var CategoryDataProvider
     */
    protected $categoryDataProvider;

    public function __construct(
        \Magento\Search\Helper\Data $searchHelper,
        CategoryDataProvider $categoryDataProvider
    ) {
        $this->searchHelper = $searchHelper;
        $this->categoryDataProvider = $categoryDataProvider;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'queryText' => $this->searchHelper->getEscapedQueryText()
        ];

        return array_merge_recursive($data, $this->categoryDataProvider->getData());
    }
}
