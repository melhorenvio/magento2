<?php

namespace MelhorEnvio\Quote\Controller\Debug;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\UrlInterface;
use MelhorEnvio\Quote\Api\Data\ShippingCalculateAddressInterface;
use MelhorEnvio\Quote\Helper\Data;
use MelhorEnvio\Quote\Api\Data\ShippingCalculateItemInterface;
use MelhorEnvio\Quote\Model\Services\ShippingCalculateFactory;

/**
 * Class Quote
 * @package MelhorEnvio\Quote\Controller\Debug
 */
class Quote extends Action
{
    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var JsonFactory
     */
    private $jsonResultFactory;
    /**
     * @var ShippingCalculateFactory
     */
    private $shippingCalculateFactory;

    /**
     * Quote constructor.
     * @param Context $context
     * @param JsonFactory $jsonResultFactory
     * @param UrlInterface $url
     * @param ShippingCalculateFactory $shippingCalculateFactory
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        UrlInterface $url,
        ShippingCalculateFactory $shippingCalculateFactory,
        Data $helperData
    ) {
        parent::__construct($context);
        $this->helperData = $helperData;
        $this->url = $url;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->shippingCalculateFactory = $shippingCalculateFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$this->helperData->enableUrlDebug()) {
            return $this->getResponse()->setRedirect(
                $this->url->getUrl('noroute')
            );
        }

        $data = [
            'from' => $this->prepareFromAddress(),
            'to' => $this->prepareToAddress(),
            'products' => $this->prepareItems(),
            'options' => $this->prepareOptions(),
            'services' => $this->helperData->getServicesAvailable()
        ];

        $quote = $this->shippingCalculateFactory->create([
            'data' => $data
        ])->doRequest()->getBodyArray();

        $result = $this->jsonResultFactory->create();
        $result->setData($quote);
        return $result;
    }

    /**
     * @return array
     */
    private function prepareFromAddress(): array
    {
        return [
            ShippingCalculateAddressInterface::POSTCODE => $this->helperData->getConfigData('from/postcode'),
            ShippingCalculateAddressInterface::STREET => $this->helperData->getConfigData('from/street'),
            ShippingCalculateAddressInterface::NUMBER => $this->helperData->getConfigData('from/number'),
        ];
    }

    /**
     * @return array
     */
    private function prepareToAddress(): array
    {
        $address[ShippingCalculateAddressInterface::POSTCODE] = '05365-040';
        $address[ShippingCalculateAddressInterface::STREET] = 'Rua Uberlândia';
        $address[ShippingCalculateAddressInterface::NUMBER] = '150';

        return $address;
    }

    /**
     * @return array
     */
    private function prepareOptions(): array
    {
        return [
            'receipt' => $this->helperData->isReceipt(),
            'collect' => $this->helperData->isCollect(),
            'own_hand' => $this->helperData->isOwnHand()
        ];
    }

    private function prepareItems()
    {
        return [
            [
                ShippingCalculateItemInterface::SKU => 'TEST-0001',
                ShippingCalculateItemInterface::WEIGHT => $this->helperData->getProductWeight($this->helperData->getConfigData('product/weight_default')),
                ShippingCalculateItemInterface::WIDTH => $this->helperData->getConfigData('product/width_default'),
                ShippingCalculateItemInterface::HEIGHT => $this->helperData->getConfigData('product/height_default'),
                ShippingCalculateItemInterface::LENGTH => $this->helperData->getConfigData('product/length_default'),
                ShippingCalculateItemInterface::QUANTITY => 1,
                ShippingCalculateItemInterface::INSURANCE_VALUE => 23.90
            ]
        ];
    }
}
