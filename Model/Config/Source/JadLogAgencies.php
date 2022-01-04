<?php

namespace MelhorEnvio\Quote\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Helper\Data;
use MelhorEnvio\Quote\Model\Services\AgenciesFactory;

/**
 * Class JadLogAgencies
 * @package MelhorEnvio\Quote\Model\Config\Source
 */
class JadLogAgencies implements OptionSourceInterface
{
    const JADLOG = 2;

    /**
     * @var AgenciesFactory
     */
    private $agencies;
    /**
     * @var Data
     */
    private $helperData;

    /**
     * JadLogAgencies constructor.
     * @param AgenciesFactory $agencies
     * @param Data $helperData
     */
    public function __construct(
        AgenciesFactory $agencies,
        Data $helperData
    ) {
        $this->agencies = $agencies;
        $this->helperData = $helperData;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $data = [];

        $agencies = $this->getAgencies();
        if (empty($agencies)) {
            return $data;
        }
        foreach ($agencies as $agency) {
            if ($agency['status'] !== 'available') {
                continue;
            }

            $data[] = [
                'value' => $agency['id'],
                'label' => sprintf(
                    '%s/%s (%s)',
                    $this->helperData->removeAccent($agency['address']['city']['city']),
                    $agency['address']['city']['state']['state_abbr'],
                    $agency['address']['address']
                )
            ];
        }
        setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
        $label = array_column($data, 'label');
        array_multisort($label, SORT_ASC, SORT_LOCALE_STRING, $data);
        return $data;
    }

    /**
     * @return array
     */
    private function getAgencies(): array
    {
        $filter['company'] = self::JADLOG;
        if ($state = $this->helperData->getConfigData('from/state')) {
            $filter['state'] = $state;
        }
        $filter['sort'] = "name:asc";
        try {
            return $this->agencies->create(['data' => $filter])->doRequest()->getBodyArray();
        } catch (LocalizedException $e) {
            return [];
        }
    }
}
