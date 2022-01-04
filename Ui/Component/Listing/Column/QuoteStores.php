<?php

namespace MelhorEnvio\Quote\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\App\Config\ScopeConfigInterface;

class QuoteStores extends Column
{
    protected $urlBuilder;
    protected $storeManager;
    protected $scopeConfig;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as & $item) {
            if(empty($item['origin'])) {
                $handle = $this->getStoreOptions($item);
                for ($i=0; $i < count($handle); $i++) {
                    $item[$this->getData('name')] = $handle;
                }
            } else {
                $item[$this->getData('name')] = [
                    'origin' => [
                        'href' => '',
                        'label' => __($item['origin'])
                    ]
                ];
            }
        }

        return $dataSource;
    }

    protected function getStoreOptions($item)
    {
        $storeName = [];
        $storeViewName = [];
        $storeView = [];
        foreach ($this->storeManager->getWebsite()->getGroups() as $key => $group) {
            $storeName[] = $group->getName();
            foreach ($group->getStores() as $store) {
                $storeViewName[] = $store->getName();
                $storeView[$store->getName()] =
                    [
                        'href' => $this->urlBuilder->getUrl(
                            'melhorenvio_quote/quote/SetStoreView',
                            [
                                'quote_id' => $item['quote_id'],
                                'origin' => $store->getCode()
                            ]
                        ),
                        'label' => __($store->getName())
                    ];

            }
        }

        return $storeView;
    }

    public function getStorePostcode()
    {
        return $this->scopeConfig->getValue(
            'general/store_information/postcode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function toOptionArray()
    {
        // TODO: Implement toOptionArray() method.
    }
}
