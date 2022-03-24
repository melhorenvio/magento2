<?php

namespace MelhorEnvio\Quote\Model\ResourceModel\InventoryReservation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MelhorEnvio\Quote\Model\InventoryReservation;
use MelhorEnvio\Quote\Model\ResourceModel\InventoryReservation as ResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'reservation_id';
    protected $_eventPrefix = 'melhorenvio_quote_inventory_reservation_collection';
    protected $_eventObject = 'inventory_reservation_collection';

    /**
     * Define the resource model & the model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(InventoryReservation::class, ResourceModel::class);
    }
}
