<?php

namespace MelhorEnvio\Quote\Model\Services;

use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\Data\HttpResponseInterface;
use MelhorEnvio\Quote\Api\ServiceInterface;
use Laminas\Http\Request as HttpRequest;

/**
 * Class SearchItem
 * @package MelhorEnvio\Quote\Model\Services
 */
class SearchItem extends AbstractService implements ServiceInterface
{
    /**
     * @inheritDoc
     */
    public function getEndpoint(): string
    {
        return $this->generateEndpoint('/api/v2/me/orders/search');
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return HttpRequest::METHOD_GET;
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
