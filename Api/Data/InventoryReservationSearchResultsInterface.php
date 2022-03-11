<?php

namespace MelhorEnvio\Quote\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface InventoryReservationSearchResultsInterface
 *
 * @package MelhorEnvio\Quote\Api\Data
 */
interface InventoryReservationSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \MelhorEnvio\Quote\Api\Data\InventoryReservationInterface[]
     */
    public function getItems();

    /**
     * @param \MelhorEnvio\Quote\Api\Data\InventoryReservationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
