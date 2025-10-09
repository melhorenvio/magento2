<?php

namespace MelhorEnvio\Quote\Model\ShippingCalculate;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;
use MelhorEnvio\Quote\Api\Data\ShippingCalculateItemInterface;
use MelhorEnvio\Quote\Api\DataProviderInterface;
use MelhorEnvio\Quote\Helper\Data;

/**
 * Class ItemDataProvider
 * @package MelhorEnvio\Quote\Model\ShippingCalculate
 */
class ItemDataProvider implements DataProviderInterface
{
    /**
     * @var Item[]
     */
    private $items;
    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    private string $service;

    /**
     * ItemDataProvider constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param Data $helperData
     * @param array $data
     * @param string $service
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Data $helperData,
        string $service,
        array $data = []
    ) {
        $this->items = $data;
        $this->service = $service;
        $this->helperData = $helperData;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        $data = [];
        $oldSkuPass = '';
        foreach ($this->items as $item) {
            try {
                $product = $this->productRepository->get($item->getSku());
            } catch (NoSuchEntityException $e) {
                continue;
            }
            if ($oldSkuPass != $item->getSku()) {
                $data[] = [
                    ShippingCalculateItemInterface::ID => $item->getSku(),
                    ShippingCalculateItemInterface::WEIGHT => $this->helperData->getProductWeight($item->getWeight()),
                    ShippingCalculateItemInterface::WIDTH => $this->getMeasurementAttrValue('width', $product),
                    ShippingCalculateItemInterface::HEIGHT => $this->getMeasurementAttrValue('height', $product),
                    ShippingCalculateItemInterface::LENGTH => $this->getMeasurementAttrValue('length', $product),
                    ShippingCalculateItemInterface::QUANTITY => (int)$item->getQty(),
                    ShippingCalculateItemInterface::INSURANCE_VALUE => $this->getInsuranceValue($product->getFinalPrice())
                ];
            }
            $oldSkuPass = $item->getSku();
        }

        return $data;
    }

    private function getInsuranceValue($productPrice): float
    {
        if(in_array($this->service, QuoteInterface::CORREIOS_SERVICE_IDS) && !$this->helperData->alwaysSafe()){
            return 0.00;
        }
        return $productPrice;
    }

    /**
     * @param $attribute
     * @param ProductInterface $product
     * @return int
     */
    private function getMeasurementAttrValue($attribute, ProductInterface $product): int
    {
        $attr = $this->getProductConfigData(sprintf('%s_attribute', $attribute));
        $valueDefault = $this->getProductConfigData(sprintf('%s_default', $attribute));
        $attrValue = $this->getValueOrDefault($product->getData($attr), $valueDefault);

        return ceil((float) $attrValue * (float) $this->helperData->getConfigData('unit_measurement'));
    }

    /**
     * @param $field
     * @return string
     */
    private function getProductConfigData($field): string
    {
        return (string) $this->helperData->getConfigData($field);
    }

    /**
     * @param mixed ...$args
     * @return mixed|null
     */
    private function getValueOrDefault(...$args)
    {
        foreach ($args as $_arg) {
            if (!!$_arg) {
                return $_arg;
            }
        }

        return null;
    }
}
