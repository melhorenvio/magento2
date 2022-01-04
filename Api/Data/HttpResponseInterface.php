<?php

namespace MelhorEnvio\Quote\Api\Data;

/**
 * Interface HttpResponseInterface
 * @package MelhorEnvio\Quote\Api\Data
 */
interface HttpResponseInterface
{
    const CODE = 'code';
    const HEADERS = 'headers';
    const BODY = 'body';

    /**
     * @return int
     */
    public function getCode(): int;

    /**
     * @return array
     */
    public function getHeaders(): array;

    /**
     * @return string|null
     */
    public function getBody(): ?string;

    /**
     * @return array
     */
    public function getBodyArray(): array;
}
