<?php

namespace MelhorEnvio\Quote\Api;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\Data\InventoryReservationInterface;
use MelhorEnvio\Quote\Api\Data\InventoryReservationSearchResultsInterface;

interface InventoryReservationRepositoryInterface
{
    /**
     * @param InventoryReservationInterface $inventoryReservation
     * @return InventoryReservationInterface
     * @throws LocalizedException
     */
    public function save(InventoryReservationInterface $inventoryReservation);

    /**
     * @param string $reservation_id
     * @return InventoryReservationInterface
     * @throws LocalizedException
     */
    public function get($reservation_id);

    /**
     * @param SearchCriteria $searchCriteria
     * @return InventoryReservationSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteria $searchCriteria);

    /**
     * @param InventoryReservationInterface $inventoryReservation
     * @return bool
     * @throws LocalizedException
     */
    public function delete(InventoryReservationInterface $inventoryReservation);

}
