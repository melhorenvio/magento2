<?php

namespace MelhorEnvio\Quote\Api\Data;

/**
 * Interface CarrierCompanyInterface
 * @package MelhorEnvio\Quote\Api\Data
 */
interface CarrierCompanyInterface
{
    const ID = 'id';
    const NAME = 'name';
    const PICTURE = 'picture';

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getPicture(): string;
}
