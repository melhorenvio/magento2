<?php

namespace MelhorEnvio\Quote\Model\Data\Carrier;

use Magento\Framework\DataObject;
use MelhorEnvio\Quote\Api\Data\CarrierCompanyInterface;

/**
 * Class Company
 * @package MelhorEnvio\Quote\Model\Data\Carrier
 */
class Company extends DataObject implements CarrierCompanyInterface
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
    public function getPicture(): string
    {
        return $this->getData(self::PICTURE);
    }
}
