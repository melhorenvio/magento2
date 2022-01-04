<?php

namespace MelhorEnvio\Quote\Api;

use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\Data\CarrierInterface;

/**
 * Interface ShippingCalculateManagementInterface
 * @package MelhorEnvio\Quote\Api
 */
interface ShippingCalculateManagementInterface
{
    /**
     * @return CarrierInterface[]
     * @throws LocalizedException
     */
    public function getAvailableServices(): array;
}
