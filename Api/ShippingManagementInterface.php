<?php

namespace MelhorEnvio\Quote\Api;

/**
 * Interface ShippingManagementInterface
 * @package MelhorEnvio\Quote\Api
 */
interface ShippingManagementInterface
{
    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    public function createShippingFromOrder(\Magento\Sales\Api\Data\OrderInterface $order);


    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return mixed
     */
    public function createShippingFromOrderResend(\Magento\Sales\Api\Data\OrderInterface $order, $quoteReverse);

    /**
     * @param string $shippingMethod
     * @return bool
     */
    public function isAvailableShippingMethod($shippingMethod);

    /**
     * @param int $shippingId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addShippingToCart($shippingId);

    /**
     * @param int $shippingId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeShippingToCart($shippingId);

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function invoiceAllShippingFromCart();

    /**
     * @param $shippingId
     * @return mixed
     */
    public function generateTags($shippingId);

    /**
     * @param $shippingId
     * @return mixed
     */
    public function previewTags($shippingId);

    /**
     * @param $shippingId
     * @return mixed
     */
    public function cancelTags($shippingId);

    /**
     * @param $data
     * @return mixed
     */
    public function clearGridShippingToCart($data);
}
