<?php

namespace MelhorEnvio\Quote\Model\Carrier;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Shipping\Model\Tracking\Result\StatusFactory;
use MelhorEnvio\Quote\Api\DataProviderInterface;
use MelhorEnvio\Quote\Helper\Data;
use MelhorEnvio\Quote\Model\Data\Carrier\Carrier;
use MelhorEnvio\Quote\Model\ShippingCalculate\DataProviderFactory;
use MelhorEnvio\Quote\Model\ShippingCalculate\ShippingCalculateManagementFactory;
use Psr\Log\LoggerInterface;

/**
 * Class MelhorEnvio
 * @package MelhorEnvio\Quote\Model\Carrier
 */
class MelhorEnvio extends AbstractCarrier implements CarrierInterface
{
    public const CODE = 'melhorenvio';

    protected $_code = self::CODE;
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;
    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;
    /**
     * @var DataProviderFactory
     */
    private $shippingCalculateDataProviderFactory;
    /**
     * @var ShippingCalculateManagementFactory
     */
    private $shippingCalculateManagementFactory;

    /**
     * @var Data
     */
    private $_helperData;

    /**
     * @var StatusFactory
     */
    private $_trackStatusFactory;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;


    /**
     * MelhorEnvio constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param DataProviderFactory $shippingCalculateDataProviderFactory
     * @param ShippingCalculateManagementFactory $shippingCalculateManagementFactory
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        DataProviderFactory $shippingCalculateDataProviderFactory,
        ShippingCalculateManagementFactory $shippingCalculateManagementFactory,
        StatusFactory $trackStatusFactory,
        Data $helperData,
        CheckoutSession $checkoutSession,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->shippingCalculateDataProviderFactory = $shippingCalculateDataProviderFactory;
        $this->shippingCalculateManagementFactory = $shippingCalculateManagementFactory;
        $this->_trackStatusFactory = $trackStatusFactory;
        $this->_helperData = $helperData;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        if (!$request->getDestPostcode()) {
            return false;
        }

        $cep = str_replace("-", "", $request->getDestPostcode());
        if (!preg_match('/\d{8}/', $cep)) {
            return false;
        }

        $services = $this->_helperData->getServicesAvailable();
        $carriers = [];
        $shippingCalculateDataProvider = $this->shippingCalculateDataProviderFactory->create([
            'data' => ['request' => $request],
            'service' => $services
        ]);

        $postData = $shippingCalculateDataProvider->getData();
        $shippingCalculate = $this->shippingCalculateManagementFactory->create([
            'data' => $postData
        ]);

        try {
            $carriers = $this->getRatesFromSession($postData);
            if (!$carriers) {
                $carriers = $shippingCalculate->getAvailableServices();
                $this->setRatesOnSession($postData, $carriers);
            }
        } catch (LocalizedException $e) {
            $this->_logger->error($e->getMessage());
        }

        return $this->prepareCarriersToOutput($carriers);
    }

    /**
     * @param array $carriers
     * @return Result
     */
    private function prepareCarriersToOutput(array $carriers): Result
    {
        $shippingDiscount = $this->getConfigData('quote/discount') ?? 0;
        $shippingTax = $this->getConfigData('quote/tax') ?? 0;
        $additionalDeliveryDays = $this->getConfigData('quote/additional_days') ?? 0;

        $result = $this->_rateResultFactory->create();

        /** @var Carrier $carrier */
        foreach ($carriers as $carrier) {
            $carrier->setDiscount($shippingDiscount);
            $carrier->setTax($shippingTax);
            $carrier->setAdditionalDeliveryDays($additionalDeliveryDays);

            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod($this->_code . '_' . $carrier->getId());
            $method->setMethodTitle(sprintf(
                '%s %s - de %d a %d dias úteis',
                $carrier->getCompany()->getName(),
                $carrier->getName(),
                $carrier->getDeliveryMin(),
                $carrier->getDeliveryMax()
            ));
            $method->setPrice($carrier->getPrice());
            $method->setCost($carrier->getPrice());

            $result->append($method);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    public function getTrackingInfo($trackingNumber)
    {
        $tracking = $this->_trackStatusFactory->create();

        $url = 'https://www.melhorrastreio.com.br/rastreio/' . $trackingNumber; // this is the tracking URL of stamps.com, replace this with your's

        $tracking->setData([
            'carrier' => $this->_code,
            'carrier_title' => $this->getConfigData('title'),
            'tracking' => $trackingNumber,
            'url' => $url,
        ]);
        return $tracking;
    }

    /**
     * @param $request
     * @return array
     */
    protected function getRatesFromSession($request)
    {
        $requestHash = md5(json_encode($request));
        $currentRates = $this->checkoutSession->getMelhorEnvioCollectRates() ?? [];
        return $currentRates[$requestHash] ?? [];
    }

    /**
     * @param $request
     * @param $response
     * @return void
     */
    protected function setRatesOnSession($request, $response)
    {
        $currentRates = $this->checkoutSession->getMelhorEnvioCollectRates();
        $requestHash = md5(json_encode($request));

        if (!isset($currentRates[$requestHash])) {
            $currentRates[$requestHash] = $response;
            $this->checkoutSession->setMelhorEnvioCollectRates($currentRates);
        }
    }
}
