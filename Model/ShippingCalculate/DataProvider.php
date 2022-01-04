<?php

namespace MelhorEnvio\Quote\Model\ShippingCalculate;

use Magento\Quote\Model\Quote\Address\RateRequest;
use MelhorEnvio\Quote\Api\Data\ShippingCalculateAddressInterface;
use MelhorEnvio\Quote\Api\DataProviderInterface;
use MelhorEnvio\Quote\Helper\Data;

/**
 * Class DataProvider
 * @package MelhorEnvio\Quote\Model\ShippingCalculate
 */
class DataProvider implements DataProviderInterface
{
    /**
     * @var RateRequest
     */
    private $rateRequest;
    /**
     * @var ItemDataProviderFactory
     */
    private $itemDataProviderFactory;
    /**
     * @var Data
     */
    private $helperData;
    private string $service;

    /**
     * DataProvider constructor.
     * @param ItemDataProviderFactory $itemDataProviderFactory
     * @param Data $helperData
     * @param array $data
     * @param string $service
     */
    public function __construct(
        ItemDataProviderFactory $itemDataProviderFactory,
        Data $helperData,
        string $service,
        array $data = []
    ) {
        $this->service = $service;
        $this->rateRequest = $data['request'];
        $this->itemDataProviderFactory = $itemDataProviderFactory;
        $this->helperData = $helperData;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return [
            'from' => $this->prepareFromAddress(),
            'to' => $this->prepareToAddress(),
            'products' => $this->prepareItems(),
            'options' => $this->prepareOptions(),
            'services' => $this->service
        ];
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
        $address[ShippingCalculateAddressInterface::POSTCODE] = $this->rateRequest->getDestPostcode();

        if (!$this->rateRequest->getDestStreet()) {
            return $address;
        }

        $street = explode(PHP_EOL, $this->rateRequest->getDestStreet());
        $address[ShippingCalculateAddressInterface::STREET] = $street[0];
        $address[ShippingCalculateAddressInterface::NUMBER] = $street[1];

        return $address;
    }

    /**
     * @return array
     */
    private function prepareItems(): array
    {
        $itemDataProvider = $this->itemDataProviderFactory->create([
            'data' =>  $this->rateRequest->getAllItems(),
            'service' => $this->service
        ]);

        return $itemDataProvider->getData();
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
}
