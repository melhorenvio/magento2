<?php

namespace MelhorEnvio\Quote\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Quote
 * @package MelhorEnvio\Quote\Model\ResourceModel
 */
class Quote extends AbstractDb
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('melhorenvio_quote', 'quote_id');
    }
}
