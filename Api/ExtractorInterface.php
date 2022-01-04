<?php

namespace MelhorEnvio\Quote\Api;

/**
 * Interface CarriersExtractorInterface
 * @package MelhorEnvio\Quote\Api
 */
interface ExtractorInterface
{
    /**
     * @return array
     */
    public function extract(): array;
}
