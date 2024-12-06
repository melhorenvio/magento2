<?php

namespace MelhorEnvio\Quote\Model\ShippingCalculate;

use MelhorEnvio\Quote\Api\ExtractorInterface;
use MelhorEnvio\Quote\Api\Data\CarrierInterface;
use MelhorEnvio\Quote\Model\Data\Carrier\CarrierFactory;
use MelhorEnvio\Quote\Model\Data\Carrier\CompanyFactory;

/**
 * Class CarriersExtractor
 * @package MelhorEnvio\Quote\Model\ShippingCalculate
 */
class CarriersExtractor implements ExtractorInterface
{
    /**
     * @var array
     */
    private $data = [];
    /**
     * @var CarrierFactory
     */
    private $carrierFactory;
    /**
     * @var CompanyFactory
     */
    private $companyFactory;

    /**
     * Extractor constructor.
     * @param CarrierFactory $carrierFactory
     * @param CompanyFactory $companyFactory
     * @param array $data
     */
    public function __construct(
        CarrierFactory $carrierFactory,
        CompanyFactory $companyFactory,
        array $data = []
    ) {
        $this->data = $data;
        $this->carrierFactory = $carrierFactory;
        $this->companyFactory = $companyFactory;
    }

    /**
     * @inheritDoc
     */
    public function extract(): array
    {
        $carriers = [];
        if (isset($this->data['price'])) {
            $carriers[] = $this->getCarrier($carriers);
        } else {
            foreach ($this->data as $carrierData) {
                try {
                    $carrier = $this->getCarrier($carrierData);
                    $carriers[] = $carrier[0];
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return $carriers;
    }

    /**
     * @param array $carriers
     * @return array
     */
    public function getCarrier(array $carrier): array
    {
        $carrierCompany = $this->companyFactory->create([
            'data' => $carrier['company']
        ]);

        $carrier[CarrierInterface::DELIVERY_MIN] = $carrier['delivery_range']['min'];
        $carrier[CarrierInterface::DELIVERY_MAX] = $carrier['delivery_range']['max'];
        $carrier[CarrierInterface::COMPANY] = $carrierCompany;

        $carriers[] = $this->carrierFactory->create([
            'data' => $carrier
        ]);

        return $carriers;
    }
}
