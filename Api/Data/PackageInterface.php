<?php

namespace MelhorEnvio\Quote\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface PackageInterface
 * @package MelhorEnvio\Quote\Api\Data
 */
interface PackageInterface extends ExtensibleDataInterface
{
    const ID = 'id';
    const QUOTE_ID = 'quote_id';
    const CODE = 'code';
    const PACKAGE_ID = 'package_id';
    const TRACKING = 'tracking';
    const PACKAGES = 'packages';
    const PROTOCOL = 'protocol';

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \MelhorEnvio\Quote\Api\Data\PackageInterface
     */
    public function setId($id);

    /**
     * Get package_id
     * @return string|null
     */
    public function getPackageId();

    /**
     * Set package_id
     * @param string $packageId
     * @return \MelhorEnvio\Quote\Api\Data\PackageInterface
     */
    public function setPackageId($packageId);

    /**
     * Get quote_id
     * @return string|null
     */
    public function getQuoteId();

    /**
     * Set quote_id
     * @param string $quoteId
     * @return \MelhorEnvio\Quote\Api\Data\PackageInterface
     */
    public function setQuoteId($quoteId);

    /**
     * Get packages
     * @return string|null
     */
    public function getPackages();

    /**
     * Set packages
     * @param string $packages
     * @return \MelhorEnvio\Quote\Api\Data\PackageInterface
     */
    public function setPackages($packages);

    /**
     * Get code
     * @return string|null
     */
    public function getCode();

    /**
     * Set code
     * @param string $code
     * @return \MelhorEnvio\Quote\Api\Data\PackageInterface
     */
    public function setCode($code);

    /**
     * Get tracking
     * @return string|null
     */
    public function getTracking();

    /**
     * Set tracking
     * @param string $tracking
     * @return \MelhorEnvio\Quote\Api\Data\PackageInterface
     */
    public function setTracking($tracking);

    /**
     * Get protocol
     * @return string|null
     */
    public function getProtocol();

    /**
     * Set protocol
     * @param string $protocol
     * @return \MelhorEnvio\Quote\Api\Data\PackageInterface
     */
    public function setProtocol($protocol);

    /**
     * @return bool
     */
    public function canAddToCart();
}
