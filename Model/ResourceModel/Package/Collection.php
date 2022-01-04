<?php

namespace MelhorEnvio\Quote\Model\ResourceModel\Package;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MelhorEnvio\Quote\Model\Package;

/**
 * Class Collection
 * @package MelhorEnvio\Quote\Model\ResourceModel\Package
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Package::class,
            \MelhorEnvio\Quote\Model\ResourceModel\Package::class
        );
    }
}
