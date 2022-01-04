<?php

namespace MelhorEnvio\Quote\Model\Message;

use DateTime;
use Magento\Framework\Notification\MessageInterface;
use MelhorEnvio\Quote\Helper\Data;

/**
 * Class TokenInvalid
 * @package MelhorEnvio\Quote\Model\Message
 */
class TokenInvalid implements MessageInterface
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * TokenExp constructor.
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @inheritDoc
     */
    public function getIdentity()
    {
        return md5('melhorenvio_quote_token_invalid');
    }

    /**
     * @inheritDoc
     */
    public function isDisplayed()
    {
        if (!$this->helperData->isActive()) {
            return false;
        }

        $payload = json_decode(base64_decode(explode('.', $this->helperData->getToken())[1]), true);
        $timestamp = $payload['exp'];
        $expDate = new DateTime();
        $expDate->setTimestamp($timestamp);

        $current = new DateTime('now');

        $interval = $current->diff($expDate);

        return (bool) $interval->days < 1;
    }

    /**
     * @inheritDoc
     */
    public function getText()
    {
        return __(
            'Os serviços da Melhor Envio foram suspensos. <a href="%1" target="_blank">Cadastre um novo Token para reativá-los</a>',
            $this->helperData->getMelhorEnvioUrl() . '/painel/gerenciar/tokens'
        );
    }

    /**
     * @inheritDoc
     */
    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }
}
