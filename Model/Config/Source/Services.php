<?php

namespace MelhorEnvio\Quote\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use MelhorEnvio\Quote\Helper\Data;
use MelhorEnvio\Quote\Model\Services\CompaniesFactory;

/**
 * Class Services
 * @package MelhorEnvio\Quote\Model\Config\Source
 */
class Services implements OptionSourceInterface
{
    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var CompaniesFactory
     */
    private $companiesFactory;

    /**
     * Services constructor.
     * @param CompaniesFactory $companiesFactory
     * @param Data $helperData
     */
    public function __construct(
        CompaniesFactory $companiesFactory,
        Data $helperData
    ) {
        $this->helperData = $helperData;
        $this->companiesFactory = $companiesFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $data = [];
        $companies = $this->companiesFactory->create()->doRequest()->getBodyArray();
        if (!is_array($companies)) {
            return $data;
        }

        foreach ($companies as $company) {
            foreach ($company['services'] as $service) {
                $data[] = [
                    'value' => $service['id'],
                    'label' => $company['name'] . ' ' . $service['name']
                ];
            }
        }

        return $data;
    }
}
