<?php

namespace MelhorEnvio\Quote\Block\Adminhtml\Quote;

use Magento\Backend\Block\Widget\Button\SplitButton;
use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\Helper\Data as HelperPrice;
use MelhorEnvio\Quote\Api\ShippingManagementInterface;
use MelhorEnvio\Quote\Helper\Data;
use MelhorEnvio\Quote\Model\Services\BalanceFactory;
use MelhorEnvio\Quote\Model\Services\CartFactory;

/**
 * Class Grid
 * @package MelhorEnvio\Quote\Block\Adminhtml\Quote
 */
class Grid extends Container
{
    /**
     * @var CartFactory
     */
    private $cartFactory;

    /**
     * @var array
     */
    private $cartInfo = [];

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var BalanceFactory
     */
    private $balanceFactory;

    /**
     * @var HelperPrice
     */
    private $helperPrice;

    /**
     * @var ShippingManagementInterface
     */
    private $shippingManagement;

    /**
     * Grid constructor.
     * @param Context $context
     * @param CartFactory $cartFactory
     * @param BalanceFactory $balanceFactory
     * @param Data $helperData
     * @param HelperPrice $helperPrice
     * @param ShippingManagementInterface $shippingManagement
     * @param array $data
     */
    public function __construct(
        Context $context,
        CartFactory $cartFactory,
        BalanceFactory $balanceFactory,
        Data $helperData,
        HelperPrice $helperPrice,
        ShippingManagementInterface $shippingManagement,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cartFactory = $cartFactory;
        $this->helperData = $helperData;
        $this->balanceFactory = $balanceFactory;
        $this->helperPrice = $helperPrice;
        $this->shippingManagement = $shippingManagement;
    }

    /**
     * @return Container
     */
    protected function _prepareLayout()
    {
        $this->buttonList->add('melhorenvio_quote_balance', [
            'id' => 'melhorenvio_quote_balance',
            'label' => $this->getBalanceLabel(),
            'class' => 'balance',
            'button_class' => '',
            'onclick' => "setLocation('" . $this->getUrlToBalance() . "')"
        ]);
        if (!$this->getCartInfo()) {
            return parent::_prepareLayout();
        }

        $cartButtonOptions = [
            'id' => 'melhorenvio_quote_cart',
            'label' => $this->getCartLabel(),
            'class' => 'secondary',
            'button_class' => 'secondary',
            'class_name' => SplitButton::class,
            'options' => $this->getCartOptions(),
        ];

        // if ($this->cartInfo['total'] < 1) {
        //     unset($cartButtonOptions['class_name']);
        //     unset($cartButtonOptions['options']);
        // }

        if($this->cartInfo['total'] > 0){
            $this->buttonList->add('melhorenvio_quote_cart', $cartButtonOptions);
        }

        if ($this->cartInfo['total']) {
            $this->buttonList->add('melhorenvio_quote_checkout', [
                'id' => 'melhorenvio_quote_checkout',
                'label' => __('Finalizar Compra'),
                'class' => 'primary',
                'button_class' => 'add',
                'onclick' => "setLocation('" . $this->getCreateCheckoutUrl('saldo') . "')"
            ]);
        }

        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    protected function getCartOptions()
    {
        return [
            [
                'label' => __('Ver Carrinho'),
                'onclick' => "setLocation('" . $this->getUrlToCart() . "')",
                'target' => '_black'
            ],
            [
                'label' => __('Limpar Carrinho'),
                'onclick' => "setLocation('" . $this->getUrl('melhorenvio_quote/quote/cleanCart') . "')",
            ]
        ];
    }

    /**
     * @param $gateway
     * @return string
     */
    protected function getCreateCheckoutUrl($gateway): string
    {
        return (string) $this->getUrl('melhorenvio_quote/quote/checkout', ['gateway' => $gateway]);
    }

    /**
     * @return array|null
     * @throws LocalizedException
     */
    private function getCartInfo()
    {
        if ($this->cartInfo == null) {
            try {
                $this->cartInfo = $this->cartFactory->create()->doRequest()->getBodyArray();
                if (!isset($this->cartInfo['data'])) {
                    $this->cartInfo['data'] = [];
                }
            } catch (LocalizedException $e) {
                $this->cartInfo = ['data' => []];
            }
        }

        return $this->cartInfo;
    }

    /**
     * @return Phrase
     */
    private function getCartLabel(): Phrase
    {
        if (isset($this->cartInfo['total'])) {
            if ($this->cartInfo['total'] == 1) {
                return __('1 Item no Carrinho');
            }

            if ($this->cartInfo['total'] > 1) {
                return __('%1 Itens no Carrinho', $this->cartInfo['total']);
            }
        }
        $this->cartInfo = [
            'total' => 0
        ];

        return __('Nenhum item no carrinho');
    }

    /**
     * @return string
     */
    private function getUrlToCart(): string
    {
        return $this->helperData->getMelhorEnvioUrl() . '/carrinho';
    }

    /**
     * @return Phrase
     */
    private function getBalanceLabel(): Phrase
    {
        $response = __('Consultar Saldo');
        try {
            $result = $this->balanceFactory->create()->doRequest();
            if ($result->getCode() == 200) {
                $data = $result->getBodyArray();
                $response = __(
                    'Saldo %1',
                    $this->helperPrice->currency($data['balance'], true, false)
                );
            }
        } catch (LocalizedException $e) {
            $response = __('Consultar Saldo');
        }

        return $response;
    }

    /**
     * @return string
     */
    private function getUrlToBalance(): string
    {
        return $this->helperData->getMelhorEnvioUrl() . '/painel/melhor-carteira';
    }
}
