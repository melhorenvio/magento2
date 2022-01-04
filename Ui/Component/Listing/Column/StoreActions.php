<?php

namespace MelhorEnvio\Quote\Ui\Component\Listing\Column;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Checkout\Model\Session as CheckoutSession;

class StoreActions extends Column
{
    protected $urlBuilder;
    protected $request;
    protected $checkoutSession;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        RequestInterface $request,
        CheckoutSession $checkoutSession,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        $quoteId = $this->checkoutSession->getQuoteId();

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [
                    'edit' => [
                        'href' => $this->urlBuilder->getUrl(
                            'melhorenvio_quote/quote/SetStoreView',
                            [
                                'quote_id' => $quoteId,
                                'melhor_store_id' => $item['melhor_store_id']
                            ]
                        ),
                        'label' => __('Save')
                    ]
                ];
            }
        }

        return $dataSource;
    }
}
