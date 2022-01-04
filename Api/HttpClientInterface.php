<?php

namespace MelhorEnvio\Quote\Api;

use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\Data\HttpResponseInterface;

/**
 * Interface HttpClientInterface
 * @package MelhorEnvio\Quote\Api
 */
interface HttpClientInterface
{
    /**
     * @param ServiceInterface $service
     * @return HttpResponseInterface
     * @throws LocalizedException
     */
    public function doRequest(ServiceInterface $service): HttpResponseInterface;
}
