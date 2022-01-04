<?php

namespace MelhorEnvio\Quote\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface PackageRepositoryInterface
 * @package MelhorEnvio\Quote\Api
 */
interface PackageRepositoryInterface
{
    /**
     * Save Package
     * @param \MelhorEnvio\Quote\Api\Data\PackageInterface $package
     * @return \MelhorEnvio\Quote\Api\Data\PackageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \MelhorEnvio\Quote\Api\Data\PackageInterface $package
    );

    /**
     * Retrieve Package
     * @param string $packageId
     * @return \MelhorEnvio\Quote\Api\Data\PackageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($packageId);

    /**
     * Retrieve Package matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MelhorEnvio\Quote\Api\Data\PackageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param int $shippingId
     * @return \MelhorEnvio\Quote\Api\Data\PackageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListByParentShippingId(int $shippingId);

    /**
     * Delete Package
     * @param \MelhorEnvio\Quote\Api\Data\PackageInterface $package
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \MelhorEnvio\Quote\Api\Data\PackageInterface $package
    );

    /**
     * Delete Package by ID
     * @param string $packageId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($packageId);

    /**
     * Get Package by Code
     * @param $code
     * @return \MelhorEnvio\Quote\Api\Data\PackageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPackageCode($code);
}
