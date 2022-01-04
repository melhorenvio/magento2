<?php

namespace MelhorEnvio\Quote\Model\Store\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Grid extends AbstractDb
{
    protected $_idFieldName = 'melhor_store_id';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('melhor_store', 'melhor_store_id');
    }
}
