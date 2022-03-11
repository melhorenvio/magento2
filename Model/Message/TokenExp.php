<?php

namespace MelhorEnvio\Quote\Model\Message;

use DateTime;
use Magento\Framework\Notification\MessageInterface;
use MelhorEnvio\Quote\Helper\Data;

/**
 * Class TokenExp
 * @package MelhorEnvio\Quote\Model\Message
 */
class TokenExp implements MessageInterface
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
        return hash('sha256', 'melhorenvio_quote_token_exp');
    }

    /**
     * @inheritDoc
     */
    public function isDisplayed()
    {
        if (!$this->helperData->isActive()) {
            return false;
        }

        if(isset(explode('.', $this->helperData->getToken())[1])){

            $payload = json_decode(base64_decode(explode('.', $this->helperData->getToken())[1]), true);
            $timestamp = $payload['exp'];
            $expDate = new DateTime();
            $expDate->setTimestamp($timestamp);
            
            $current = new DateTime('now');
            
            $interval = $current->diff($expDate);
            
            return (bool) ($interval->days > 1 && $interval->days < 30);

        }else{
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function getText()
    {
        return __(
            'Cadastre um novo token na Melhor Envio para manter o serviço funcionando. <a href="%1" target="_blank">Gerar um novo Token</a>',
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
