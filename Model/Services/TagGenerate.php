<?php

namespace MelhorEnvio\Quote\Model\Services;

use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\Data\HttpResponseInterface;
use MelhorEnvio\Quote\Api\ServiceInterface;
use Zend_Http_Client;

/**
 * Class TagGenerate
 * @package MelhorEnvio\Quote\Model\Services
 */
class TagGenerate extends AbstractService implements ServiceInterface
{
    /**
     * @inheritDoc
     */
    public function getEndpoint(): string
    {
        return $this->generateEndpoint('/api/v2/me/shipment/generate');
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return Zend_Http_Client::POST;
    }

    /**
     * @return HttpResponseInterface
     * @throws LocalizedException
     */
    public function doRequest(): HttpResponseInterface
    {
        return $this->httpClient->doRequest($this);
    }
}
