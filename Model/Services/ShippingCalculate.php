<?php

namespace MelhorEnvio\Quote\Model\Services;

use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\Data\HttpResponseInterface;
use MelhorEnvio\Quote\Api\ServiceInterface;

/**
 * Class ShippingCalculate
 * @package MelhorEnvio\Quote\Model\Services
 */
class ShippingCalculate extends AbstractService implements ServiceInterface
{
    /**
     * @inheritDoc
     */
    public function getEndpoint(): string
    {
        return $this->generateEndpoint('/api/v2/me/shipment/calculate');
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return \Laminas\Http\Request::METHOD_POST;
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
