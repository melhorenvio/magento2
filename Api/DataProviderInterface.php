<?php

namespace MelhorEnvio\Quote\Api;

/**
 * Interface DataProviderInterface
 * @package MelhorEnvio\Quote\Api
 */
interface DataProviderInterface
{
    const ATTRIBUTE = 'dataProvider';

    /**
     * @return array
     */
    public function getData(): array;
}
