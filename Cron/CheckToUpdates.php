<?php

namespace MelhorEnvio\Quote\Cron;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Convert\Order as ConvertOrder;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Shipping\Model\ShipmentNotifier;
use MelhorEnvio\Quote\Api\Data\PackageInterface;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;
use MelhorEnvio\Quote\Api\LoggerInterface;
use MelhorEnvio\Quote\Api\PackageRepositoryInterface;
use MelhorEnvio\Quote\Api\QuoteRepositoryInterface;
use MelhorEnvio\Quote\Model\Carrier\MelhorEnvio;
use MelhorEnvio\Quote\Model\ResourceModel\Quote\CollectionFactory;
use MelhorEnvio\Quote\Model\Services\CartFactory;
use MelhorEnvio\Quote\Model\Services\SearchItemFactory;

/**
 * Class CheckToUpdates
 * @package MelhorEnvio\Quote\Cron
 */
class CheckToUpdates
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var SearchItemFactory
     */
    private $searchItemFactory;
    /**
     * @var CartFactory
     */
    private $cartFactory;
    /**
     * @var ConvertOrder
     */
    private $convertOrder;
    /**
     * @var Order
     */
    private $order;
    /**
     * @var ShipmentNotifier
     */
    private $shipmentNotifier;
    /**
     * @var TrackFactory
     */
    private $trackFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var PackageRepositoryInterface
     */
    private $packageRepository;

    /**
     * @var PackageInterface
     */
    private $packageInterface;

    /**
     * Status constructor.
     * @param CollectionFactory $collectionFactory
     * @param QuoteRepositoryInterface $quoteRepository
     * @param SearchItemFactory $searchItemFactory
     * @param CartFactory $cartFactory
     * @param Order $order
     * @param ConvertOrder $convertOrder
     * @param ShipmentNotifier $shipmentNotifier
     * @param TrackFactory $trackFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionFactory          $collectionFactory,
        QuoteRepositoryInterface   $quoteRepository,
        SearchItemFactory          $searchItemFactory,
        CartFactory                $cartFactory,
        Order                      $order,
        ConvertOrder               $convertOrder,
        ShipmentNotifier           $shipmentNotifier,
        TrackFactory               $trackFactory,
        LoggerInterface            $logger,
        PackageRepositoryInterface $packageRepository,
        PackageInterface           $packageInterface
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->quoteRepository = $quoteRepository;
        $this->searchItemFactory = $searchItemFactory;
        $this->cartFactory = $cartFactory;
        $this->convertOrder = $convertOrder;
        $this->order = $order;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->trackFactory = $trackFactory;
        $this->logger = $logger;
        $this->packageRepository = $packageRepository;
        $this->packageInterface = $packageInterface;
    }

    /**
     *
     */
    public function execute()
    {
        $this->logger->info('CRON START');
        $packageCode = '';

        foreach ($this->getAllItemsAvailableToStatusChange() as $item) {
            $this->logger->info($item->getStatus());
            try {
                $packageCollection = $this->packageRepository->getListByParentShippingId($item->getQuoteId());
                foreach ($packageCollection->getItems() as $package) {
                    $packageCode = $package->getCode();
                    $currentData = $this->getRemoteData($packageCode);
                    if (empty($currentData)) {
                        $this->handleRemoveToCart($item->getId());
                    }
                }
            } catch (LocalizedException $e) {
                $this->handleRemoveToCart($item->getId());
                $this->logger->error($e->getMessage());

                continue;
            }

            if ((empty($currentData))
                && $item->getStatus() == QuoteInterface::STATUS_PENDING
            ) {
                $inCart = false;
                $allItemsInCart = $this->getAllItemsInCart();

                if ($allItemsInCart === false) {
                    continue;
                }

                foreach ($allItemsInCart as $value) {
                    if ($value['id'] == $item->getCartId()) {
                        $inCart = true;
                    } else {
                        $this->logger->info('NO CART');
                        $this->logger->info($item->getId());

                        $this->handleRemoveToCart($item->getId());
                    }
                }
            }

            if (empty($currentData) || $currentData == 'not found') {
                continue;
            }


            $this->updateItem($item->getQuoteId(), $currentData);



            if ($currentData['self_tracking']) {
                $this->handleTrackingCode(
                    $item->getOrderIncrementId(),
                    $currentData['self_tracking'],
                    $item->getDescription()
                );
            }
        }


        $this->logger->info('CRON DONE');
    }

    /**
     * @return \MelhorEnvio\Quote\Model\ResourceModel\Quote\Collection
     */
    private function getAllItemsAvailableToStatusChange()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect(QuoteInterface::QUOTE_ID);
        $collection->addFieldToSelect(QuoteInterface::STATUS);
        $collection->addFieldToSelect(QuoteInterface::ORDER_INCREMENT_ID);
        $collection->addFieldToSelect(QuoteInterface::DESCRIPTION);
        $collection->addFieldToFilter(QuoteInterface::QUOTE_ID, ['notnull' => true]);
        $collection->addFieldToFilter(QuoteInterface::STATUS, ['nin' => [
            QuoteInterface::STATUS_CANCELED,
            'delivered',
            QuoteInterface::STATUS_PENDING,
            QuoteInterface::STATUS_NEW
        ]]);

        return $collection;
    }

    /**
     * @param $cartId
     * @return array
     * @throws LocalizedException
     */
    private function getRemoteData($cartId): array
    {
        $data = $this->searchItemFactory->create(['data' => ['q' => $cartId]])
            ->doRequest()
            ->getBodyArray();

        $data = reset($data);

        return is_array($data) ? $data : [];
    }

    /**
     * @param $cartId
     * @param array $currentData
     */
    private function updateItem($cartId, array $currentData): void
    {
        try {
            $shipping = $this->quoteRepository->getByCartId($cartId);
            $shipping->setStatus($currentData['status']);

            if (array_key_exists('tracking', $currentData)) {
                $this->packageInterface->setTracking($currentData['tracking']);
            }

            if (array_key_exists('expired_at', $currentData)) {
                $shipping->setValidate($currentData['expired_at']);
            }

            $this->quoteRepository->save($shipping);
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param $cartId
     */
    private function handleRemoveToCart($id): void
    {
        try {
            $shipping = $this->quoteRepository->get($id);
            $shipping->setValidate(0);
            $shipping->setStatus(QuoteInterface::STATUS_NEW);
            $this->quoteRepository->save($shipping);
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @return bool|array
     */
    private function getAllItemsInCart()
    {
        try {
            $result = $this->cartFactory->create()->doRequest()->getBodyArray();
            return $result['data'];
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }

        return false;
    }

    /**
     * @param $incrementId
     * @param $trackingCode
     * @param string $description
     */
    private function handleTrackingCode($incrementId, $trackingCode, $description = ''): void
    {
        $order = $this->order->loadByIncrementId($incrementId);
        if (!$order->canShip()) {
            return;
        }

        $shipment = $this->convertOrder->toShipment($order);

        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }

            $qtyShipped = $orderItem->getQtyToShip();

            try {
                $shipmentItem = $this->convertOrder
                    ->itemToShipmentItem($orderItem)
                    ->setQty($qtyShipped);
            } catch (LocalizedException $e) {
                $this->logger->error($e->getMessage());
                return;
            }

            $shipment->addItem($shipmentItem);
        }

        try {
            $shipment->register();
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $shipment->getOrder()->setIsInProcess(true);

        try {
            $track = $this->trackFactory->create()->addData([
                'carrier_code' => MelhorEnvio::CODE,
                'title' => $description,
                'number' => $trackingCode
            ]);
            $shipment->addTrack($track)->save();

            $shipment->save();
            $shipment->getOrder()->save();

            $this->shipmentNotifier->notify($shipment);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return;
        }
    }
}
