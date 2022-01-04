<?php

namespace MelhorEnvio\Quote\Api;

use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\Data\HttpResponseInterface;

/**
 * Interface ServiceInterface
 * @package MelhorEnvio\Quote\Api
 */
interface ServiceInterface
{
    /**
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return array
     */
    public function getHeaders(): array;

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @return HttpResponseInterface
     * @throws LocalizedException
     */
    public function doRequest(): HttpResponseInterface;
}
