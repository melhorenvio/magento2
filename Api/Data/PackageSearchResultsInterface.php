<?php

namespace MelhorEnvio\Quote\Api\Data;

/**
 * Interface PackageSearchResultsInterface
 *
 * @package MelhorEnvio\Quote\Api\Data
 */
interface PackageSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Package list.
     * @return \MelhorEnvio\Quote\Api\Data\PackageInterface[]
     */
    public function getItems();

    /**
     * Set order_id list.
     * @param \MelhorEnvio\Quote\Api\Data\PackageInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
