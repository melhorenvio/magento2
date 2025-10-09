<?php

namespace MelhorEnvio\Quote\Api\Data;

use DateTime;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface QuoteInterface
 * @package MelhorEnvio\Quote\Api\Data
 */
interface QuoteInterface extends ExtensibleDataInterface
{
    const QUOTE_ID = 'quote_id';
    const DESCRIPTION = 'description';
    const COST = 'cost';
    const ORDER_ID = 'order_id';
    const ORDER_INCREMENT_ID = 'order_increment_id';
    const SERVICE = 'service';
    const STATUS = 'status';
    const ADDITIONAL_DATA = 'additional_data';
    const NF_KEY = 'nf_key';
    const VALIDATE = 'validate';
    const QUOTE_REVERSE = 'quote_reverse';

    const STATUS_NEW = 'new';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';
    const STATUS_PAID = 'paid';
    const ORIGIN= 'origin';

    const CORREIOS_SERVICE_IDS = [1, 2, 17];
    const JADLOG_SERVICE_IDS = [3, 4];
    const MULTIPLE_TAG_SERVICE_IDS = [1, 2, 17, 31, 32, 33, 34];

    /**
     * Get quote_id
     * @return string|null
     */
    public function getId();

    /**
     * Set quote_id
     * @param string $id
     * @return \MelhorEnvio\Quote\Api\Data\QuoteInterface
     */
    public function setId($id);

    /**
     * Get quote_id
     * @return string|null
     */
    public function getQuoteId();

    /**
     * Set quote_id
     * @param string $quoteId
     * @return \MelhorEnvio\Quote\Api\Data\QuoteInterface
     */
    public function setQuoteId($quoteId);

    /**
     * Get cost
     * @return string|null
     */
    public function getCost();

    /**
     * Set cost
     * @param string $cost
     * @return \MelhorEnvio\Quote\Api\Data\QuoteInterface
     */
    public function setCost($cost);

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $orderId
     * @return \MelhorEnvio\Quote\Api\Data\QuoteInterface
     */
    public function setOrderId($orderId);

    /**
     * Get order_increment_id
     * @return string|null
     */
    public function getOrderIncrementId();

    /**
     * Set order_increment_id
     * @param string $orderIncrementId
     * @return \MelhorEnvio\Quote\Api\Data\QuoteInterface
     */
    public function setOrderIncrementId($orderIncrementId);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \MelhorEnvio\Quote\Api\Data\QuoteInterface
     */
    public function setDescription($description);

    /**
     * Get service
     * @return string|null
     */
    public function getService();

    /**
     * Set service
     * @param string $service
     * @return \MelhorEnvio\Quote\Api\Data\QuoteInterface
     */
    public function setService($service);

    /**
     * @return string|null
     */
    public function getStatus();

    /**
     * @param string $status
     * @return \MelhorEnvio\Quote\Api\Data\QuoteInterface
     */
    public function setStatus($status);

    /**
     * Get additional data
     * @return string|null
     */
    public function getAdditionalData();

    /**
     * Append additional data
     * @param array $data
     * @return \MelhorEnvio\Quote\Api\Data\QuoteInterface
     */
    public function appendAdditionalData(array $data = []);

    /**
     * Set additional data
     * @param string $data
     * @return \MelhorEnvio\Quote\Api\Data\QuoteInterface
     */
    public function setAdditionalData($data);

    /**
     * @return string|null
     */
    public function getNfKey(): ?string;

    /**
     * @param $nfKey
     * @return \MelhorEnvio\Quote\Api\Data\QuoteInterface
     */
    public function setNfKey($nfKey);

    /**
     * @return string|null
     */
    public function getValidate(): ?string;

    /**
     * @param $validate
     * @return \MelhorEnvio\Quote\Api\Data\QuoteInterface
     */
    public function setValidate($validate);

    /**
     * @return bool
     */
    public function canInsurance();

    /**
     * @return bool
     */
    public function isJadlogService();

    /**
     * @return bool
     */
    public function isCorreioService();

    /**
     * @return bool
     */
    public function createTagsByVolumes();

    /**
     * @return mixed
     */
    public function setQuoteReverse($quoteReverse);

    /**
     * @return mixed
     */
    public function getQuoteReverse();

    /**
     * @return mixed
     */
    public function setOrigin($origin);

    /**
     * @return mixed
     */
    public function getOrigin();

}
