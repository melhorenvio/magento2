<?php

namespace MelhorEnvio\Quote\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface QuoteSearchResultsInterface
 * @package MelhorEnvio\Quote\Api\Data
 */
interface QuoteSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get Quote list.
     * @return QuoteInterface[]
     */
    public function getItems();

    /**
     * Set order_id list.
     * @param QuoteInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
