<?php

namespace MelhorEnvio\Quote\Model\Services;

use MelhorEnvio\Quote\Api\HttpClientInterface;
use MelhorEnvio\Quote\Helper\Data;

/**
 * Class AbstractService
 * @package MelhorEnvio\Quote\Model\Services
 */
abstract class AbstractService
{
    /**
     * @var Array
     */
    protected $data = [];
    /**
     * @var Data
     */
    protected $helperData;
    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * AbstractService constructor.
     * @param HttpClientInterface $httpClient
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        HttpClientInterface $httpClient,
        Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->httpClient = $httpClient;
        $this->data = $data;
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function generateEndpoint(string $endpoint): string
    {
        return $this->helperData->getMelhorEnvioUrl() . $endpoint;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $this->helperData->getToken())
        ];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
