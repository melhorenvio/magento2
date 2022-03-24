<?php

namespace MelhorEnvio\Quote\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface InventoryReservationInterface
 * @package MelhorEnvio\Quote\Api\Data
 */
interface InventoryReservationInterface extends ExtensibleDataInterface
{
    const RESERVATION_ID = 'reservation_id';
    const STOCK_ID = 'stock_id';
    const SKU = 'sku';
    const QUANTITY = 'quantity';
    const METADATA = 'metadata';

    /**
     * @return string|null
     */
    public function getReservationId();

    /**
     * @param $reservationId
     * @return \MelhorEnvio\Quote\Api\Data\InventoryReservationInterface
     */
    public function setReservationId($reservationId);

    /**
     * @return string|null
     */
    public function getStockId();

    /**
     * @param $stockId
     * @return \MelhorEnvio\Quote\Api\Data\InventoryReservationInterface
     */
    public function setStockId($stockId);

    /**
     * @return string|null
     */
    public function getSku();

    /**
     * @param $sku
     * @return \MelhorEnvio\Quote\Api\Data\InventoryReservationInterface
     */
    public function setSku($sku);

    /**
     * @return string|null
     */
    public function getQuantity();

    /**
     * @param $quantity
     * @return \MelhorEnvio\Quote\Api\Data\InventoryReservationInterface
     */
    public function setQuantity($quantity);

    /**
     * @return string|null
     */
    public function getMetadata();

    /**
     * @param $metadata
     * @return \MelhorEnvio\Quote\Api\Data\InventoryReservationInterface
     */
    public function setMetadata($metadata);
}
