<?php

namespace MelhorEnvio\Quote\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class WeightUnit
 * @package MelhorEnvio\Quote\Model\Config\Source
 */
class WeightUnit implements OptionSourceInterface
{
    const GRAM_MULTIPLICATION_FACTOR = 1000;
    const KILO_MULTIPLICATION_FACTOR = 1;

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::GRAM_MULTIPLICATION_FACTOR, 'label' => __('Gram')],
            ['value' => SELF::KILO_MULTIPLICATION_FACTOR, 'label' => __('Kilo')]
        ];
    }
}
