<?php

namespace MelhorEnvio\Quote\Model\Data\Carrier;

use Magento\Framework\DataObject;
use MelhorEnvio\Quote\Api\Data\CarrierCompanyInterface;
use MelhorEnvio\Quote\Api\Data\CarrierInterface;

/**
 * Class Carrier
 * @package MelhorEnvio\Quote\Model\Data\Carrier
 */
class Carrier extends DataObject implements CarrierInterface
{
    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function getPrice(): float
    {
        $this->applyTax();
        $priceWithDiscount = $this->getData(self::PRICE) - $this->getData(self::DISCOUNT);

        return ($priceWithDiscount > 0) ? $priceWithDiscount : 0;
    }

    /**
     * @return void
     */
    private function applyTax(): void
    {
        $priceWithTax = $this->getData(self::PRICE) + $this->getData(self::TAX);
        $this->setData(self::PRICE, $priceWithTax);
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryMin(): int
    {
        return $this->getData(self::DELIVERY_MIN) + $this->getData(self::ADDITIONAL_DELIVERY_DAYS);
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryMax(): int
    {
        return $this->getData(self::DELIVERY_MAX) + $this->getData(self::ADDITIONAL_DELIVERY_DAYS);
    }

    /**
     * @inheritDoc
     */
    public function getCompany(): CarrierCompanyInterface
    {
        return $this->getData(self::COMPANY);
    }

    /**
     * @inheritDoc
     */
    public function setDiscount(float $value): void
    {
        $this->setData(self::DISCOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTax(float $value): void
    {
        $this->setData(self::TAX, $value);
    }

    /**
     * @inheritDoc
     */
    public function setAdditionalDeliveryDays(int $days): void
    {
        $this->setData(self::ADDITIONAL_DELIVERY_DAYS, $days);
    }
}
