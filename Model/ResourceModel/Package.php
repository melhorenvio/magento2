<?php

namespace MelhorEnvio\Quote\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Package
 * @package MelhorEnvio\Quote\Model\ResourceModel
 */
class Package extends AbstractDb
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('melhorenvio_package', 'id');
    }
}
