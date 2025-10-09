<?php

namespace MelhorEnvio\Quote\Observer\Sales;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use MelhorEnvio\Quote\Helper\Data;

/**
 * Class ShipmentSaveBefore
 * @package MelhorEnvio\Quote\Observer\Sales
 */
class OrderSaveBefore implements ObserverInterface
{
    const SHIPPING_MODULE_NAME_KEY = 0;
    const SHIPPING_SERVICE_NAME = 1;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * OrderSaveBefore constructor.
     * @param CheckoutSession $checkoutSession
     * @param Data $helperData
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Data $helperData
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helperData = $helperData;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getData('order');

        $data = $this->checkoutSession->getData('melhor_envio_quote');

        if ($data) {
            $order->setMelhorenvioShipping($data);
            $this->checkoutSession->unsMelhorEnvioCollectRates();
        }

        $shippingDescription = $order->getShippingDescription();
        $shippingInfo = explode(' - ', $shippingDescription);

        if (!$this->isMelhorEnvioShipping($shippingInfo)) {
            return;
        }

        $order->setShippingDescription(sprintf(
            '%s - via %s',
            $shippingInfo[self::SHIPPING_SERVICE_NAME],
            $shippingInfo[self::SHIPPING_MODULE_NAME_KEY]
        ));

        return;
    }

    /**
     * @param array $shippingInfo
     * @return bool
     */
    private function isMelhorEnvioShipping(array $shippingInfo): bool
    {
        return $shippingInfo[self::SHIPPING_MODULE_NAME_KEY] == $this->helperData->getConfigData('name');
    }
}
