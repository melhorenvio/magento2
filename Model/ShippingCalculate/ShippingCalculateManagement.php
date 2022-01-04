<?php

namespace MelhorEnvio\Quote\Model\ShippingCalculate;

use Magento\Checkout\Model\Session;
use MelhorEnvio\Quote\Api\LoggerInterface;
use MelhorEnvio\Quote\Api\ShippingCalculateManagementInterface;
use MelhorEnvio\Quote\Model\Services\ShippingCalculateFactory;

/**
 * Class ShippingCalculateManagement
 * @package MelhorEnvio\Quote\Model\ShippingCalculate
 */
class ShippingCalculateManagement implements ShippingCalculateManagementInterface
{
    /**
     * @var Array
     */
    private $data;
    /**
     * @var ShippingCalculateFactory
     */
    private $shippingCalculateFactory;
    /**
     * @var CarriersExtractorFactory
     */
    private $carriersExtractorFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * ShippingCalculateManagement constructor.
     * @param ShippingCalculateFactory $shippingCalculateFactory
     * @param CarriersExtractorFactory $carriersExtractorFactory
     * @param Session $checkoutSession
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        ShippingCalculateFactory $shippingCalculateFactory,
        CarriersExtractorFactory $carriersExtractorFactory,
        Session $checkoutSession,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->data = $data;
        $this->shippingCalculateFactory = $shippingCalculateFactory;
        $this->carriersExtractorFactory = $carriersExtractorFactory;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @inheritDoc
     */
    public function getAvailableServices(): array
    {
        $this->logger->info(__('SHIPPING CALCULATE'), [
            'REQUEST' => print_r($this->data, true)
        ]);

        $shippingCalculateService = $this->shippingCalculateFactory->create([
            'data' => $this->data
        ]);

        $services = $shippingCalculateService->doRequest()->getBodyArray();

        $this->saveServicesInSession($services);

        $this->logger->info(__('SHIPPING CALCULATE'), [
            'RESPONSE' => print_r($services, true)
        ]);

        $carriersExtractor = $this->carriersExtractorFactory->create([
            'data' => $services
        ]);

        return $carriersExtractor->extract();
    }

    /**
     * @param $services
     */
    private function saveServicesInSession($services): void
    {
        $checkoutData = $this->checkoutSession->getData('melhor_envio_quote');
        $checkoutData = json_decode($checkoutData, true);
        $checkoutData[] = $services;
        $this->checkoutSession->setData('melhor_envio_quote', json_encode($checkoutData));
    }
}
