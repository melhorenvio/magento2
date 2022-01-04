<?php

namespace MelhorEnvio\Quote\Model\Services\Client;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClientFactory;
use MelhorEnvio\Quote\Api\Data\HttpResponseInterface;
use MelhorEnvio\Quote\Api\LoggerInterface;
use MelhorEnvio\Quote\Api\ServiceInterface;
use MelhorEnvio\Quote\Api\HttpClientInterface;
use MelhorEnvio\Quote\Model\Data\Http\ResponseFactory;
use Zend_Http_Client;
use Zend_Http_Client_Exception;

/**
 * Class ZendClient
 * @package MelhorEnvio\Quote\Model\Services\Client
 */
class ZendClient implements HttpClientInterface
{
    /**
     * @var ZendClientFactory
     */
    private $zendClientFactory;
    /**
     * @var ResponseFactory
     */
    private $httpResponseFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ZendClient constructor.
     * @param ZendClientFactory $zendClientFactory
     * @param ResponseFactory $httpResponseFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        ZendClientFactory $zendClientFactory,
        ResponseFactory $httpResponseFactory,
        LoggerInterface $logger
    ) {
        $this->productMetadata = $productMetadata;
        $this->zendClientFactory = $zendClientFactory;
        $this->httpResponseFactory = $httpResponseFactory;
        $this->logger = $logger;
    }

    /**
     * @param ServiceInterface $service
     * @return HttpResponseInterface
     * @throws LocalizedException
     */
    public function doRequest(ServiceInterface $service): HttpResponseInterface
    {
        if ($service->getMethod() == Zend_Http_Client::DELETE) {
            return $this->handleMethodDelete($service);
        }

        /** @var \Magento\Framework\HTTP\ZendClient $client */
        $client = $this->zendClientFactory->create();
        $headers = $service->getHeaders();
        $headers['User-Agent'] = 'Magento 2/'.$this->productMetadata->getVersion();


        try {
            $client->setUri($this->getEndpoint($service));
            $client->setMethod($service->getMethod());
            $client->setHeaders($service->getHeaders());

            if (!empty($service->getData())
                && $service->getMethod() != Zend_Http_Client::GET
            ) {
                $client->setRawData($this->prepareData($service->getData()));
            }

            $this->logger->info($this->getEndpoint($service), [$service->getData()]);
            $this->logger->info('headers', $headers);
            $result = $client->request();
        } catch (Zend_Http_Client_Exception $e) {
            $this->logger->error($e->getMessage());
            throw new LocalizedException(__('Resource temporarily unavailable'));
        }

        $this->logger->info($result->getBody());

        return $this->httpResponseFactory->create(['data' => [
            'code' => $result->getStatus(),
            'body' => $result->getBody(),
            'headers' => $result->getHeaders()
        ]]);
    }

    /**
     * @param ServiceInterface $service
     * @return HttpResponseInterface
     */
    private function handleMethodDelete(ServiceInterface $service): HttpResponseInterface
    {
        $header = $service->getHeaders();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getEndpoint($service),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => Zend_Http_Client::DELETE,
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => [
                'User-Agent: Magento 2/'.$this->productMetadata->getVersion(),
                'accept: application/json',
                'content-type: application/json',
                sprintf('authorization: %s', $header['Authorization'])
            ],
        ));

        curl_exec($curl);

        $info = curl_getinfo($curl);

        curl_close($curl);

        return $this->httpResponseFactory->create(['data' => [
            'code' => $info['http_code']
        ]]);
    }

    /**
     * @param array $data
     * @return string
     */
    private function prepareData(array $data): string
    {
        return json_encode($data);
    }

    /**
     * @param ServiceInterface $service
     * @return string
     */
    private function getEndpoint(ServiceInterface $service): string
    {
        if (in_array($service->getMethod(), [Zend_Http_Client::GET, Zend_Http_Client::DELETE])
            && !empty($service->getData())
        ) {
            $queryString = $service->getEndpoint() . '?' . http_build_query($service->getData());
            return str_replace('?0=', '/', $queryString);
        }

        return $service->getEndpoint();
    }
}
