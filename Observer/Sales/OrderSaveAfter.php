<?php

namespace MelhorEnvio\Quote\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use MelhorEnvio\Quote\Api\ShippingManagementInterface;

/**
 * Class OrderSaveAfter
 * @package MelhorEnvio\Quote\Observer\Sales
 */
class OrderSaveAfter implements ObserverInterface
{
    /**
     * @var ShippingManagementInterface
     */
    private $shippingManagement;

    /**
     * OrderSaveAfter constructor.
     * @param ShippingManagementInterface $shippingManagement
     */
    public function __construct(
        ShippingManagementInterface $shippingManagement
    ) {
        $this->shippingManagement = $shippingManagement;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getData('order');
        $shippingMethod = $order->getShippingMethod();

        if ($this->shippingManagement->isAvailableShippingMethod($shippingMethod)) {
            $this->shippingManagement->createShippingFromOrder($order);
        }

        return;
    }
}
