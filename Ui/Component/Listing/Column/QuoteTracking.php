<?php

namespace MelhorEnvio\Quote\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class QuoteTracking
 * @package MelhorEnvio\Quote\Ui\Component\Listing\Column
 */
class QuoteTracking extends Column
{
    protected $urlBuilder;
    /**
     * @var OrderFactory
     */
    private $order;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderFactory $order
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderFactory $order,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->order = $order;
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
            foreach ($dataSource['data']['items'] as $key => $item) {
                $order = $this->order->create()->loadByIncrementId($item['order_increment_id']);
                $trackCollection = $order->getTracksCollection();
                $trackItems = $trackCollection->getItems();
                foreach($trackItems as $track){
                    $dataSource['data']['items'][$key]['tracking'] = $track->getTrackNumber();
                    $dataSource['data']['items'][$key]['tracking_link'] = 'https://www.melhorrastreio.com.br/rastreio/' . $track->getTrackNumber();
                }
            }
        }
        return $dataSource;
    }
}
