<?php

namespace MelhorEnvio\Quote\Api;

use Magento\Framework\Exception\LocalizedException;

/**
 * Interface PackageManagementInterface
 * @package MelhorEnvio\Quote\Api
 */
interface PackageManagementInterface
{
    /**
     * @param \MelhorEnvio\Quote\Api\Data\PackageInterface $package
     * @param \MelhorEnvio\Quote\Api\Data\QuoteInterface $shipping
     * @return void
     */
    public function addPackageToCart(
        \MelhorEnvio\Quote\Api\Data\PackageInterface $package,
        \MelhorEnvio\Quote\Api\Data\QuoteInterface $shipping
    );

    /**
     * @param \MelhorEnvio\Quote\Api\Data\PackageInterface $package
     * @return void
     */
    public function removePackageToCart(
        \MelhorEnvio\Quote\Api\Data\PackageInterface $package
    );

    /**
     * @return array
     */
    public function getPackagesAvailableToInvoice();

    /**
     * @param Data\PackageInterface $package
     * @return mixed
     */
    public function generateTagsPackage(
        \MelhorEnvio\Quote\Api\Data\PackageInterface $package
    );

    /**
     * @param $shippingId
     * @return mixed
     */
    public function previewTagPackage($shippingId);

    /**
     * @param Data\PackageInterface $package
     * @return mixed
     */
    public function cancelTagsPackage(\MelhorEnvio\Quote\Api\Data\PackageInterface $package);

    /**
     * @param $package
     * @return mixed
     */
    public function removePackageRepository($package);

    /**
     * @return ResourceModel\Package\Collection
     */
    public function getPackagesToInvoiceCollection();

    /**
     * @param $code
     * @return mixed
     */
    public function removePackageToAPI($code);
}
