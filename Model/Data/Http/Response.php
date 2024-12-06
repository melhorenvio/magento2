<?php

namespace MelhorEnvio\Quote\Model\Data\Http;

use Magento\Framework\DataObject;
use MelhorEnvio\Quote\Api\Data\HttpResponseInterface;

/**
 * Class Response
 * @package MelhorEnvio\Quote\Model\Data\Http
 */
class Response extends DataObject implements HttpResponseInterface
{
    /**
     * @inheritDoc
     */
    public function getCode(): int
    {
        return $this->getData(self::CODE);
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->getData(self::HEADERS);
    }

    /**
     * @inheritDoc
     */
    public function getBody(): ?string
    {
        return $this->getData(self::BODY);
    }

    /**
     * @return array
     */
    public function getBodyArray(): array
    {
        if (empty($this->getBody())) {
            return [];
        }

        return json_decode($this->getBody(), true) ?? [];
    }
}
