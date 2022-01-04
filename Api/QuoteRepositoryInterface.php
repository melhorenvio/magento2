<?php

namespace MelhorEnvio\Quote\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;
use MelhorEnvio\Quote\Api\Data\QuoteSearchResultsInterface;

/**
 * Interface QuoteRepositoryInterface
 * @package MelhorEnvio\Quote\Api
 */
interface QuoteRepositoryInterface
{
    /**
     * Save Quote
     * @param QuoteInterface $quote
     * @return QuoteInterface
     * @throws LocalizedException
     */
    public function save(QuoteInterface $quote);

    /**
     * Retrieve Quote
     * @param string $quoteId
     * @return QuoteInterface
     * @throws LocalizedException
     */
    public function get($quoteId);

    /**
     * Retrieve Quote
     * @param string $cartId
     * @return QuoteInterface
     * @throws LocalizedException
     */
    public function getByCartId($cartId);

    /**
     * @param $orderId
     * @return bool|int
     */
    public function hasRecordAssociatedWithTheOrderId($orderId);

    /**
     * Retrieve Quote matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return QuoteSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Quote
     * @param QuoteInterface $quote
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(QuoteInterface $quote);

    /**
     * Delete Quote by ID
     * @param string $quoteId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($quoteId);
}
