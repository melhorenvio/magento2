<?php

namespace MelhorEnvio\Quote\Model\Data;

use DateTime;
use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\Framework\Api\AttributeValueFactory;
use MelhorEnvio\Quote\Api\Data\QuoteExtensionInterface;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;

/**
 * Class Quote
 * @package MelhorEnvio\Quote\Model\Data
 */
class Quote extends AbstractExtensibleObject implements QuoteInterface
{
    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->_get(self::QUOTE_ID);
    }

    /**
     * @param string $id
     * @return QuoteInterface
     */
    public function setId($id)
    {
        return $this->setData(self::QUOTE_ID, $id);
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
     * @return QuoteInterface
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->_get(self::ORDER_ID);
    }

    /**
     * Set order_id
     * @param string $orderId
     * @return QuoteInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @return string|null
     */
    public function getOrderIncrementId()
    {
        return $this->_get(self::ORDER_INCREMENT_ID);
    }

    /**
     * @param string $orderIncrementId
     * @return QuoteInterface
     */
    public function setOrderIncrementId($orderIncrementId)
    {
        return $this->setData(self::ORDER_INCREMENT_ID, $orderIncrementId);
    }

    /**
     * Get service
     * @return string|null
     */
    public function getService()
    {
        return $this->_get(self::SERVICE);
    }

    /**
     * Set service
     * @param string $service
     * @return QuoteInterface
     */
    public function setService($service)
    {
        return $this->setData(self::SERVICE, $service);
    }

    /**
     * @inheritDoc
     */
    public function getCost()
    {
        return $this->_get(self::COST);
    }

    /**
     * @inheritDoc
     */
    public function setCost($cost)
    {
        return $this->setData(self::COST, $cost);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return string|null
     */
    public function getNfKey(): ?string
    {
        return $this->_get(self::NF_KEY);
    }

    /**
     * @param $nfKey
     * @return QuoteInterface
     */
    public function setNfKey($nfKey)
    {
        return $this->setData(self::NF_KEY, $nfKey);
    }

    /**
     * @return string|null
     */
    public function getValidate(): ?string
    {
        return $this->_get(self::VALIDATE);
    }

    /**
     * @param $validate
     * @return QuoteInterface
     */
    public function setValidate($validate)
    {
        return $this->setData(self::VALIDATE, $validate);
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalData()
    {
        return $this->_get(self::ADDITIONAL_DATA);
    }

    /**
     * @inheritDoc
     */
    public function appendAdditionalData(array $data = [])
    {
        $origData = json_decode($this->getAdditionalData() ?? [], true);
        if (is_array($origData)) {
            $data = array_merge($origData, $data);
        }

        return $this->setAdditionalData(json_encode($data));
    }

    /**
     * @inheritDoc
     */
    public function setAdditionalData($data)
    {
        return $this->setData(self::ADDITIONAL_DATA, $data);
    }

    /**
     * @inheritDoc
     */
    public function canInsurance()
    {
        return $this->isJadlogService();
    }

    /**
     * @inheritDoc
     */
    public function isJadlogService()
    {
        return in_array($this->getService(), self::JADLOG_SERVICE_IDS);
    }

    /**
     * @inheritDoc
     */
    public function isCorreioService()
    {
        return in_array($this->getService(), self::CORREIOS_SERVICE_IDS);
    }

    /**
     * @inheritDoc
     */
    public function createTagsByVolumes()
    {
        return in_array($this->getService(), self::MULTIPLE_TAG_SERVICE_IDS);
    }

    /**
     * @param $quoteReverse
     * @return Quote|mixed
     */
    public function setQuoteReverse($quoteReverse)
    {
        return $this->setData(self::QUOTE_REVERSE, $quoteReverse);
    }

    /**
     * @return mixed|null
     */
    public function getQuoteReverse()
    {
        return $this->_get(self::QUOTE_REVERSE);
    }

    public function setOrigin($origin)
    {
        return $this->setData(self::ORIGIN, $origin);
    }

    public function getOrigin()
    {
        return $this->_get(self::ORIGIN);
    }
}
