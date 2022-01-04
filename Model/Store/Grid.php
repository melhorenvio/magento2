<?php

namespace MelhorEnvio\Quote\Model\Store;

use MelhorEnvio\Quote\Api\Data\StoreInterface;

class Grid extends \Magento\Framework\Model\AbstractModel implements StoreInterface
{
    const CACHE_TAG = 'melhor_store';
    protected $_cacheTag = 'melhor_store';
    protected $_eventPrefix = 'melhor_store_records';

    protected function _construct()
    {
        $this->_init('MelhorEnvio\Quote\Model\Store\ResourceModel\Grid');
    }

    public function getMelhorStoreId(): int
    {
        return $this->getData(self::MELHOR_STORE_ID);
    }

    public function setMelhorStoreId($melhorStoreId)
    {
        return $this->setData(self::MELHOR_STORE_ID, $melhorStoreId);
    }

    public function getMelhorStoreName(): string
    {
        return $this->getData(self::MELHOR_STORE_NAME);
    }

    public function setMelhorStoreName($melhorStoreName)
    {
        return $this->setData(self::MELHOR_STORE_NAME, $melhorStoreName);
    }

    public function getMelhorStorePostcode(): string
    {
        return $this->getData(self::MELHOR_STORE_POSTCODE);
    }

    public function setMelhorStorePostcode($melhorStorePostcode)
    {
        return $this->setData(self::MELHOR_STORE_POSTCODE, $melhorStorePostcode);
    }

    public function getMelhorStoreStreet(): string
    {
        return $this->getData(self::MELHOR_STORE_STREET);
    }

    public function setMelhorStoreStreet($melhorStoreStreet)
    {
        return $this->setData(self::MELHOR_STORE_STREET, $melhorStoreStreet);
    }

    public function getMelhorStoreNumber(): string
    {
        return $this->getData(self::MELHOR_STORE_NUMBER);
    }

    public function setMelhorStoreNumber($melhorStoreNumber)
    {
        return $this->setData(self::MELHOR_STORE_NUMBER, $melhorStoreNumber);
    }

    public function getMelhorStoreComplement(): string
    {
        return $this->getData(self::MELHOR_STORE_COMPLEMENT);
    }

    public function setMelhorStoreComplement($melhorStoreComplement)
    {
        return $this->setData(self::MELHOR_STORE_COMPLEMENT, $melhorStoreComplement);
    }

    public function getMelhorStoreDistrict(): string
    {
        return $this->getData(self::MELHOR_STORE_DISTRICT);
    }

    public function setMelhorStoreDistrict($melhorStoreDistrict)
    {
        return $this->setData(self::MELHOR_STORE_DISTRICT, $melhorStoreDistrict);
    }

    public function getMelhorStoreCity(): string
    {
        return $this->getData(self::MELHOR_STORE_CITY);
    }

    public function setMelhorStoreCity($melhorStoreCity)
    {
        return $this->setData(self::MELHOR_STORE_CITY, $melhorStoreCity);
    }

    public function getMelhorStoreState(): string
    {
        return $this->getData(self::MELHOR_STORE_STATE);
    }

    public function setMelhorStoreState($melhorStoreState)
    {
        return $this->setData(self::MELHOR_STORE_STATE, $melhorStoreState);
    }

    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getMelhorStoreKey(): string
    {
        return $this->getData(self::MELHOR_STORE_KEY);
    }

    public function setMelhorStoreKey($melhorStoreKey)
    {
        return $this->setData(self::MELHOR_STORE_KEY, $melhorStoreKey);
    }
}
