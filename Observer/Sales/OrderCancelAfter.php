<?php
/**
 * Trezo Soluções Web
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to https://www.trezo.com.br for more information.
 *
 * @category Trezo
 * @package MelhorEnvio_Quote
 *
 * @copyright Copyright (c) 2020 Trezo Soluções Web. (https://www.trezo.com.br)
 *
 * @author Trezo Core Team <contato@trezo.com.br>
 * @author Geovan Brambilla <brambilla.geovan@trezo.com.br>
 */

namespace MelhorEnvio\Quote\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use MelhorEnvio\Quote\Model\ResourceModel\Quote\Collection;
use MelhorEnvio\Quote\Model\ResourceModel\Quote\CollectionFactory;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;
use MelhorEnvio\Quote\Api\QuoteRepositoryInterface;
use MelhorEnvio\Quote\Api\ShippingManagementInterface;

/**
 * Class OrderCancelAfter
 * @package MelhorEnvio\Quote\Observer\Sales
 */
class OrderCancelAfter implements ObserverInterface
{
    /** @var string Quote */
    const STATUS_CANCELED = 'canceled';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var ShippingManagementInterface
     */
    private $shippingManagement;

    /**
     * @var ManagerInterface
     */
    private $message;
    /**
     * OrderCancelAfter constructor.
     * @param CollectionFactory $collectionFactory
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        QuoteRepositoryInterface $quoteRepository,
        ShippingManagementInterface $shippingManagement,
        ManagerInterface $message
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->quoteRepository = $quoteRepository;
        $this->shippingManagement = $shippingManagement;
        $this->message = $message;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getData('order');
        $orderStatus = $order->getStatus();

        if ($orderStatus == self::STATUS_CANCELED) {
            $quoteResult = $this->collection($order->getId());

            if ($quoteResult->getData()) {
                $quoteId = $quoteResult->getData()[0][QuoteInterface::QUOTE_ID];
                $this->removeToCart($quoteId);
                $quoteResult = $this->quoteRepository->get($quoteId);
                $quoteResult->setStatus(QuoteInterface::STATUS_CANCELED);
                $this->quoteRepository->save($quoteResult);
            }
        }

        return;
    }

    /**
     * @param $orderId
     * @return Collection
     */
    public function collection($orderId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect(QuoteInterface::STATUS);
        $collection->addFieldToSelect(QuoteInterface::QUOTE_ID);
        $collection->addFieldToFilter(QuoteInterface::ORDER_ID, $orderId);

        return $collection;
    }

    /**
     * Remove quote in cart
     * @param $quoteId
     * @return bool
     */
    public function removeToCart($quoteId)
    {
        try {
            $this->shippingManagement->removeShippingToCart($quoteId);
        } catch (LocalizedException $e) {
            $this->message->addErrorMessage(
                __('Fail in remove to cart', $quoteId)
            );
        }

        return true;
    }
}
