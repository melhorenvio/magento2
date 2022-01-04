<?php

namespace MelhorEnvio\Quote\Ui\Component\Listing\Column;

use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class QuotePrice
 * @package MelhorEnvio\Quote\Ui\Component\Listing\Column
 */
class QuotePrice extends Column
{
    /**
     * @var Data
     */
    private $priceHelper;

    /**
     * QuotePrice constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Data $priceHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Data $priceHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->priceHelper = $priceHelper;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['cost'])) {
                    $item['cost'] = $this->priceHelper->currency($item['cost'],true,false);
                }
            }
        }

        return $dataSource;
    }
}
