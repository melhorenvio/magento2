<?php

namespace MelhorEnvio\Quote\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class QuoteActions
 * @package MelhorEnvio\Quote\Ui\Component\Listing\Column
 */
class QuoteActions extends Column
{
    const URL_PATH_EDIT = 'melhorenvio_quote/quote/edit';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
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
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['status'] == 'new') {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'quote_id' => $item['quote_id']
                                ]
                            ),
                            'label' => __('Chave NFe')
                        ],
                        'origin' => [
                            'href' => $this->urlBuilder->getUrl(
                                'melhorenvio_quote/store/index',
                                [
                                    'quote_id' => $item['quote_id']
                                ]
                            ),
                            'label' => __('Escolher Origem')
                        ],
                    ];
                } else {
                    $item[$this->getData('name')] = [
                        'resend' => [
                            'href' => $this->urlBuilder->getUrl(
                                'melhorenvio_quote/quote/resend',
                                [
                                    'quote_id' => $item['quote_id']
                                ]
                            ),
                            'label' => __('Gerar Novo Envio')
                        ],
                        'reverse' => [
                            'href' => $this->urlBuilder->getUrl(
                                'melhorenvio_quote/quote/reverseQuote',
                                [
                                    'quote_id' => $item['quote_id']
                                ]
                            ),
                            'label' => __('Frete Reverso')
                        ],
                    ];

                    if ($item['status'] == 'Cancelado') {
                        continue;
                    }

                    $item[$this->getData('name')] = array_merge([
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'quote_id' => $item['quote_id']
                                ]
                            ),
                            'label' => __('Chave NFe')
                        ],
                        'tag_preview' => [
                            'href' => $this->urlBuilder->getUrl(
                                'melhorenvio_quote/quote/tagPreview',
                                [
                                    'action' => 'preview',
                                    'quote_id' => $item['quote_id']
                                ]
                            ),
                            'target' => '_blank',
                            'label' => __('Ver Etiqueta')
                        ],
                        'tag_generate' => [
                            'href' => $this->urlBuilder->getUrl(
                                'melhorenvio_quote/quote/tag',
                                [
                                    'action' => 'generate',
                                    'quote_id' => $item['quote_id']
                                ]
                            ),
                            'label' => __('Gerar Etiqueta')
                        ],
                        'cancel' => [
                            'href' => $this->urlBuilder->getUrl(
                                'melhorenvio_quote/quote/cancel',
                                [
                                    'quote_id' => $item['quote_id'],
                                    'order_id' => $item['order_id']
                                ]
                            ),
                            'label' => __('Cancelar'),
                            'confirm' => [
                                'title' => __('Cancelar Frete %1', $item['quote_id']),
                                'message' => __(
                                    'Tem certeza que deseja cancelar o frete %1?',
                                    $item['quote_id']
                                )
                            ],
                            'post' => true
                        ]
                    ], $item[$this->getData('name')]);
                }
            }
        }

        return $dataSource;
    }
}
