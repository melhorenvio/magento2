<?php

namespace MelhorEnvio\Quote\Model;

use DateInterval;
use DateTime;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use MelhorEnvio\Quote\Api\Data\PackageInterface;
use MelhorEnvio\Quote\Api\Data\PackageInterfaceFactory;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;
use MelhorEnvio\Quote\Api\Data\QuoteInterfaceFactory;
use MelhorEnvio\Quote\Api\PackageManagementInterface;
use MelhorEnvio\Quote\Api\PackageRepositoryInterface;
use MelhorEnvio\Quote\Api\QuoteRepositoryInterface;
use MelhorEnvio\Quote\Api\ShippingManagementInterface;
use MelhorEnvio\Quote\Model\Carrier\MelhorEnvio;
use MelhorEnvio\Quote\Model\Services\CartFactory;
use MelhorEnvio\Quote\Model\Services\CheckoutFactory as ServiceCheckoutFactory;

/**
 * Class ShippingManagement
 * @package MelhorEnvio\Quote\Model
 */
class ShippingManagement implements ShippingManagementInterface
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var QuoteInterfaceFactory
     */
    private $quoteFactory;
    /**
     * @var PackageRepositoryInterface
     */
    private $packageRepository;
    /**
     * @var PackageInterfaceFactory
     */
    private $packageFactory;
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var PackageManagementInterface
     */
    private $packageManagement;
    /**
     * @var ServiceCheckoutFactory
     */
    private $serviceCheckoutFactory;
    private CartFactory $cartFactory;

    /**
     * QuoteManagement constructor.
     * @param QuoteRepositoryInterface $quoteRepository
     * @param QuoteInterfaceFactory $quoteFactory
     * @param PackageRepositoryInterface $packageRepository
     * @param PackageInterfaceFactory $packageFactory
     * @param PackageManagementInterface $packageManagement
     * @param ServiceCheckoutFactory $serviceCheckoutFactory
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        QuoteRepositoryInterface $quoteRepository,
        QuoteInterfaceFactory $quoteFactory,
        PackageRepositoryInterface $packageRepository,
        PackageInterfaceFactory $packageFactory,
        PackageManagementInterface $packageManagement,
        ServiceCheckoutFactory $serviceCheckoutFactory,
        CheckoutSession $checkoutSession,
        CartFactory $cartFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->packageRepository = $packageRepository;
        $this->packageFactory = $packageFactory;
        $this->checkoutSession = $checkoutSession;
        $this->packageManagement = $packageManagement;
        $this->serviceCheckoutFactory = $serviceCheckoutFactory;
        $this->cartFactory = $cartFactory;
    }

    /**
     * @inheritDoc
     */
    public function isAvailableShippingMethod($shippingMethod)
    {
        return $this->extractMethodName($shippingMethod) == MelhorEnvio::CODE;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function createShippingFromOrder(OrderInterface $order)
    {
        if (!$this->isNewOrder($order)) {
            return;
        }

        $serviceId = $this->extractServiceId($order->getShippingMethod());
        $packages = $this->extractPackagesFromService($serviceId);

        if (!$packages) {
            throw new LocalizedException(__('Not found packages in shipping'));
        }
        $quoteReverse = 0;
        $quote = $this->createShippingQuote($order, $quoteReverse);

        if ($quote->createTagsByVolumes()) {
            foreach ($packages as $data) {
                $this->createPackage($quote->getId(), $data);
            }
        } else {
            $this->createPackage($quote->getId(), $packages);
        }

        return;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function createShippingFromOrderResend(OrderInterface $order, $quoteReverse)
    {
        $serviceId = $this->extractServiceId($order->getShippingMethod());
        $packages = $this->extractPackagesFromServiceResend($serviceId, $order->getMelhorenvioShipping());

        if (!$packages) {
            throw new LocalizedException(__('Not found packages in shipping'));
        }

        $quote = $this->createShippingQuote($order, $quoteReverse);

        if ($quote->isCorreioService()) {
            foreach ($packages as $data) {
                $this->createPackage($quote->getId(), $data);
            }
        } else {
            $this->createPackage($quote->getId(), $packages);
        }

        return;
    }

    /**
     * @param $serviceId
     * @return array
     */
    public function extractPackagesFromServiceResend($serviceId, $data): array
    {
        if (empty($data)) {
            return [];
        }

        $services = json_decode($data, true);
        foreach ($services as $service) {
            if ($service['id'] != $serviceId) {
                continue;
            }

            if (array_key_exists('packages', $service)
                && is_array($service['packages'])
            ) {
                return $service['packages'];
            }
        }

        return [];
    }

    /**
     * @param $serviceId
     * @return array
     */
    public function extractPackagesFromService($serviceId): array
    {
        $data = $this->checkoutSession->getData('melhor_envio_quote');
        if (empty($data)) {
            return [];
        }

        $services = json_decode($data, true);
        foreach ($services as $service) {
            if (!isset($service['id']) || $service['id'] != $serviceId) {
                continue;
            }

            if (
                array_key_exists('packages', $service)
                && is_array($service['packages'])
            ) {
                return $service['packages'];
            }
        }

        return [];
    }

    /**
     * @param OrderInterface $order
     * @param $quoteReverse
     * @return QuoteInterface
     * @throws LocalizedException
     */
    private function createShippingQuote(OrderInterface $order, $quoteReverse)
    {
        /** @var QuoteInterface $quote */
        $quote = $this->quoteFactory->create();

        $serviceId = $this->extractServiceId($order->getShippingMethod());
        $quote->setOrderId($order->getEntityId());
        $quote->setOrderIncrementId($order->getIncrementId());
        $quote->setDescription($order->getShippingDescription());
        $quote->setService($serviceId);
        $quote->setCost($order->getShippingAmount());
        $quote->setStatus(QuoteInterface::STATUS_NEW);
        if ($quoteReverse != 1) {
            $quoteReverse = 0;
        }

        $quote->setQuoteReverse($quoteReverse);
        $quote = $this->quoteRepository->save($quote);

        return $quote;
    }

    /**
     * @param int $quoteId
     * @param array $data
     * @return string|null
     * @throws LocalizedException
     */
    public function createPackage(int $quoteId, $data = [])
    {
        /** @var PackageInterface $package */
        $package = $this->packageFactory->create();

        $package->setQuoteId($quoteId);
        $package->setPackages(json_encode($data));

        $package = $this->packageRepository->save($package);

        return $package->getId();
    }

    /**
     * @param string $shippingMethod
     * @return string
     */
    private function extractMethodName($shippingMethod)
    {
        $method = explode('_', $shippingMethod);

        return reset($method);
    }

    /**
     * @param string $shippingMethod
     * @return string
     */
    private function extractServiceId($shippingMethod)
    {
        $method = explode('_', $shippingMethod);

        return $method[2];
    }

    /**
     * @param OrderInterface $order
     * @return bool
     */
    private function isNewOrder(OrderInterface $order)
    {
        return !$this->quoteRepository->hasRecordAssociatedWithTheOrderId($order->getEntityId());
    }

    /**
     * @param int $shippingId
     * @return void
     * @throws LocalizedException
     */
    public function addShippingToCart($shippingId)
    {
        $shipping = $this->quoteRepository->get($shippingId);
        $packageCollection = $this->packageRepository->getListByParentShippingId($shippingId);

        if ($shipping->getStatus() == \MelhorEnvio\Quote\Observer\Sales\OrderCancelAfter::STATUS_CANCELED) {
            throw new LocalizedException(__('O frete %1 não pode ser adicionado ao carrinho (Pedido cancelado)', $shippingId));
        }
        foreach ($packageCollection->getItems() as $package) {
            if (!$package->canAddToCart()) {
                throw new LocalizedException(__('O frete %1 não pode ser adicionado ao carrinho', $shippingId));
            }

            $this->packageManagement->addPackageToCart($package, $shipping);
        }

        $shipping->setStatus('pending');
        $this->quoteRepository->save($shipping);

        return;
    }

    /**
     * @inheritDoc
     */
    public function removeShippingToCart($shippingId)
    {
        $shipping = $this->quoteRepository->get($shippingId);
        $packageCollection = $this->packageRepository->getListByParentShippingId($shippingId);

        if ($shipping->getStatus() == QuoteInterface::STATUS_PAID) {
            throw new LocalizedException(__('Não foi possível remover o carrinho'));
        }
        foreach ($packageCollection->getItems() as $package) {
            if ($package->getProtocol()) {
                $this->packageManagement->removePackageToCart($package);
            }
        }

        $shipping->setStatus('new');
        $this->quoteRepository->save($shipping);

        return;
    }

    /**
     * @inheritDoc
     */
    public function invoiceAllShippingFromCart()
    {
        $packageCodes = [];
        $shippingList = [];

        $cart = [];
        $cartService = $this->cartFactory->create();
        $response = $cartService->doRequest();
        $cartShippings = $response->getBodyArray();

        foreach($cartShippings['data'] as $cartShipping){
            $cart[$cartShipping['id']] = $cartShipping;
        }

        $packages = $this->packageManagement->getPackagesAvailableToInvoice();
        foreach ($packages as $item) {
            $shipping = $this->quoteRepository->get($item->getQuoteId());
            if ($shipping->getStatus() == 'pending' && isset($cart[$item->getCode()])) {
                $packageCodes[] = $item->getCode();
                $shippingList[] = $shipping;
            }
        }

        $request = $this->serviceCheckoutFactory->create(['data' => [
            'orders' => $packageCodes
        ]])->doRequest();

        if ($request->getCode() >= 300) {
            throw new LocalizedException(__($request->getBodyArray()['error']));
        }

        $data = $request->getBodyArray();
        $additionalData = $data['purchase'];

        foreach ($shippingList as $shipping) {
            try {
                $shipping->setStatus($additionalData['status']);
                $shipping->appendAdditionalData($additionalData);

                $currentDate = new DateTime('now');
                $expireDate = $currentDate->add(new DateInterval('P7D'));

                $shipping->setValidate($expireDate->format('d/m/Y'));

                $this->quoteRepository->save($shipping);
            } catch (LocalizedException $e) {
            }
        }

        return;
    }

    /**
     * @param $shippingId
     * @return mixed|void
     * @throws LocalizedException
     */
    public function generateTags($shippingId)
    {
        $shipping = $this->quoteRepository->get($shippingId);
        $packageCollection = $this->packageRepository->getListByParentShippingId($shippingId);

        foreach ($packageCollection->getItems() as $package) {
            if ($package->getProtocol()) {
                $this->packageManagement->generateTagsPackage($package);
            }
        }
        return;
    }

    /**
     * @param $shippingId
     * @return mixed
     */
    public function previewTags($shippingId)
    {
        return $this->packageManagement->previewTagPackage($shippingId);
    }

    public function cancelTags($shippingId)
    {
        $packageCollection = $this->packageRepository->getListByParentShippingId($shippingId);
        try {
            foreach ($packageCollection->getItems() as $package) {
                if ($package->getProtocol()) {
                    $this->packageManagement->cancelTagsPackage($package);
                }
            }
            $shipping = $this->quoteRepository->get($shippingId);
            $shipping->setStatus(QuoteInterface::STATUS_CANCELED);
            $this->quoteRepository->save($shipping);
        } catch (LocalizedException $e) {
            throw new LocalizedException(__('Não foi possível cancelar o fretec'));
        }

        return;
    }

    /**
     * @param $data
     * @return bool|mixed|string
     * @throws LocalizedException
     */
    public function clearGridShippingToCart($data)
    {
        $codeApi = [];
        foreach ($data as $package) {
            $codeApi[] = $package['id'];
        }

        $packages = $this->packageManagement->getPackagesToInvoiceCollection();
        $codeRepository = [];
        foreach ($packages->getItems() as $item) {
            $codeRepository = [$item->getCode()];
            if (!in_array($item->getCode(), $codeApi) && $item->getProtocol()) {
                $quoteId = $item->getQuoteId();
                $shipping = $this->quoteRepository->get($quoteId);
                if ($shipping->getStatus() == QuoteInterface::STATUS_PENDING) {
                    $this->packageManagement->removePackageRepository($item);
                    $shipping->setStatus(QuoteInterface::STATUS_NEW);
                    $this->quoteRepository->save($shipping);
                }
            }
        }

        $clearApi = true;
        foreach ($codeApi as $code) {
            if (!in_array($code, $codeRepository)) {
                $this->packageManagement->removePackageToAPI($code);
                $clearApi = 'clear';
            }
        }

        return $clearApi;
    }
}
