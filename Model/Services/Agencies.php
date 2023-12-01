<?php

namespace MelhorEnvio\Quote\Model\Services;

use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\Data\HttpResponseInterface;
use MelhorEnvio\Quote\Api\ServiceInterface;

/**
 * Class Agencies
 * @package MelhorEnvio\Quote\Model\Services
 */
class Agencies extends AbstractService implements ServiceInterface
{
    /**
     * @inheritDoc
     */
    public function getEndpoint(): string
    {
        return $this->generateEndpoint('/api/v2/me/shipment/agencies');
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return \Laminas\Http\Request::METHOD_GET;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
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
