<?php

namespace MelhorEnvio\Quote\Logger;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Handler
 * @package MelhorEnvio\Quote\Logger
 */
class Handler extends Base
{
    protected $loggerType = 'info';

    protected $fileName = '/var/log/melhorenvio.log';
}
