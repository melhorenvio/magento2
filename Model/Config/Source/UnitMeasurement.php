<?php

namespace MelhorEnvio\Quote\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class UnitMeasurement
 * @package MelhorEnvio\Quote\Model\Config\Source
 */
class UnitMeasurement implements OptionSourceInterface
{
    const METER_MULTIPLICATION_FACTOR = 100;
    const CENTIMETER_MULTIPLICATION_FACTOR = 1;

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::METER_MULTIPLICATION_FACTOR, 'label' => __('Meter')],
            ['value' => SELF::CENTIMETER_MULTIPLICATION_FACTOR, 'label' => __('Centimeter')]
        ];
    }
}
