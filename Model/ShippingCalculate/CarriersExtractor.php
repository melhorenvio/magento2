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
        if(isset($this->data['price'])){
            $carrierCompany = $this->companyFactory->create([
                'data' => $this->data['company']
            ]);

            $this->data[CarrierInterface::DELIVERY_MIN] = $this->data['delivery_range']['min'];
            $this->data[CarrierInterface::DELIVERY_MAX] = $this->data['delivery_range']['max'];
            $this->data[CarrierInterface::COMPANY] = $carrierCompany;

            $carriers[] = $this->carrierFactory->create([
                'data' => $this->data
            ]);
        }

        return $carriers;
    }
}
