<?php

namespace MelhorEnvio\Quote\Model\ResourceModel\Quote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;
use MelhorEnvio\Quote\Model\Quote;

/**
 * Class Collection
 * @package MelhorEnvio\Quote\Model\ResourceModel\Quote
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'quote_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Quote::class,
            \MelhorEnvio\Quote\Model\ResourceModel\Quote::class
        );
    }
}
