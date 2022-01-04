<?php

namespace MelhorEnvio\Quote\Model\Store\ResourceModel\Grid;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = 'melhor_store_id';

    protected function _construct()
    {
        $this->_init('MelhorEnvio\Quote\Model\Store\Grid', 'MelhorEnvio\Quote\Model\Store\ResourceModel\Grid');
    }
}
