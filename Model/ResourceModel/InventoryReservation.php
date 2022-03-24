<?php
namespace MelhorEnvio\Quote\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class InventoryReservation extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('inventory_reservation', 'reservation_id');
    }
}
