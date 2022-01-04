<?php

namespace MelhorEnvio\Quote\Api\Data;

/**
 * Interface CarrierInterface
 * @package MelhorEnvio\Quote\Api\Data
 */
interface CarrierInterface
{
    const ID = 'id';
    const NAME = 'name';
    const PRICE = 'price';
    const DISCOUNT = 'discount';
    const TAX = 'tax';
    const DELIVERY_MIN = 'delivery_min';
    const DELIVERY_MAX = 'delivery_max';
    const ADDITIONAL_DELIVERY_DAYS = 'additional_delivery_days';
    const COMPANY = 'company';

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return float
     */
    public function getPrice(): float;

    /**
     * @return int
     */
    public function getDeliveryMin(): int;

    /**
     * @return int
     */
    public function getDeliveryMax(): int;

    /**
     * @return CarrierCompanyInterface
     */
    public function getCompany(): CarrierCompanyInterface;

    /**
     * @param float $value
     * @return void
     */
    public function setDiscount(float $value): void;

    /**
     * @param float $value
     * @return void
     */
    public function setTax(float $value): void;

    /**
     * @param int $days
     * @return void;
     */
    public function setAdditionalDeliveryDays(int $days): void;
}
