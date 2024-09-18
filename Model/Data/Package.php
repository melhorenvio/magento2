<?php

namespace MelhorEnvio\Quote\Model\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use MelhorEnvio\Quote\Api\Data\PackageInterface;

/**
 * Class Package
 * @package MelhorEnvio\Quote\Model\Data
 */
class Package extends AbstractSimpleObject implements PackageInterface
{
    protected $getProtocol;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @param string $id
     * @return PackageInterface|void
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get package_id
     * @return string|null
     */
    public function getPackageId()
    {
        return $this->_get(self::PACKAGE_ID);
    }

    /**
     * Set package_id
     * @param string $packageId
     * @return PackageInterface
     */
    public function setPackageId($packageId)
    {
        return $this->setData(self::PACKAGE_ID, $packageId);
    }

    /**
     * Get quote_id
     * @return string|null
     */
    public function getQuoteId()
    {
        return $this->_get(self::QUOTE_ID);
    }

    /**
     * Set quote_id
     * @param string $quoteId
     * @return PackageInterface
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * Get packages
     * @return string|null
     */
    public function getPackages()
    {
        return $this->_get(self::PACKAGES);
    }

    /**
     * Set packages
     * @param string $packages
     * @return PackageInterface
     */
    public function setPackages($packages)
    {
        return $this->setData(self::PACKAGES, $packages);
    }

    /**
     * Get code
     * @return string|null
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * Set code
     * @param string $code
     * @return PackageInterface
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * Get tracking
     * @return string|null
     */
    public function getTracking()
    {
        return $this->_get(self::TRACKING);
    }

    /**
     * Set tracking
     * @param string $tracking
     * @return PackageInterface
     */
    public function setTracking($tracking)
    {
        return $this->setData(self::TRACKING, $tracking);
    }

    /**
     * Get protocol
     * @return string|null
     */
    public function getProtocol()
    {
        return $this->_get(self::PROTOCOL);
    }

    /**
     * Set protocol
     * @param string $protocol
     * @return PackageInterface
     */
    public function setProtocol($protocol)
    {
        return $this->setData(self::PROTOCOL, $protocol);
    }

    /**
     * @return bool
     */
    public function canAddToCart()
    {

        if ($this->getProtocol() || $this->getProtocol() == '' || $this->getProtocol != 'null') {
            return true;
        } else {
            return false;
        }
    }

}
