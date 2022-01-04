<?php

namespace MelhorEnvio\Quote\Model\Services;

use MelhorEnvio\Quote\Api\Data\HttpResponseInterface;
use MelhorEnvio\Quote\Api\HttpClientInterface;
use MelhorEnvio\Quote\Api\StoreInterface;
use MelhorEnvio\Quote\Helper\Data as Helper;
use Zend_Http_Client;


class Stores extends AbstractService implements StoreInterface
{
    protected $helper;
    protected $companies;
    protected $httpClient;

    public function __construct(
        Helper $helper,
        Companies $companies,
        HttpClientInterface $httpClient
    ) {
        $this->helper = $helper;
        $this->companies = $companies;
        $this->httpClient = $httpClient;
    }

    public function getEndpoint(): string
    {
        return $this->generateEndpoint('/api/v2/me/companies');
    }

    public function getMethod(): string
    {
        return Zend_Http_Client::GET;
    }

    public function doRequest(): HttpResponseInterface
    {
        return $this->httpClient->doRequest($this);
    }
}
