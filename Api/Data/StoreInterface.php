<?php

namespace MelhorEnvio\Quote\Api\Data;

interface StoreInterface
{
    const MELHOR_STORE_ID        = 'melhor_store_id';
    const MELHOR_STORE_NAME      = 'melhor_store_name';
    const MELHOR_STORE_KEY       = 'melhor_store_key';
    const MELHOR_STORE_POSTCODE  = 'melhor_store_postcode';
    const MELHOR_STORE_STREET    = 'melhor_store_street';
    const MELHOR_STORE_NUMBER    = 'melhor_store_number';
    const MELHOR_STORE_COMPLEMENT= 'melhor_store_complement';
    const MELHOR_STORE_DISTRICT  = 'melhor_store_district';
    const MELHOR_STORE_CITY      = 'melhor_store_city';
    const MELHOR_STORE_STATE     = 'melhor_store_state';
    const UPDATE_TIME            = 'update_time';
    const CREATED_AT             = 'created_at';

    /**
     * @return int
     */
    public function getMelhorStoreId(): int;
    public function setMelhorStoreId($melhorStoreId);

    /**
     * @return string
     */
    public function getMelhorStoreName(): string;
    public function setMelhorStoreName($melhorStoreName);

    /**
     * @return string
     */
    public function getMelhorStoreKey(): string;
    public function setMelhorStoreKey($melhorStoreKey);

    /**
     * @return string
     */
    public function getMelhorStorePostcode(): string;
    public function setMelhorStorePostcode($melhorStorePostcode);

    /**
     * @return string
     */
    public function getMelhorStoreStreet(): string;
    public function setMelhorStoreStreet($melhorStoreStreet);

    /**
     * @return string
     */
    public function getMelhorStoreNumber(): string;
    public function setMelhorStoreNumber($melhorStoreNumber);

    /**
     * @return string
     */
    public function getMelhorStoreComplement(): string;
    public function setMelhorStoreComplement($melhorStoreComplement);

    /**
     * @return string
     */
    public function getMelhorStoreDistrict(): string;
    public function setMelhorStoreDistrict($melhorStoreDistrict);

    /**
     * @return string
     */
    public function getMelhorStoreCity(): string;
    public function setMelhorStoreCity($melhorStoreCity);

    /**
     * @return string
     */
    public function getMelhorStoreState(): string;
    public function setMelhorStoreState($melhorStoreState);


    /**
     * Get UpdateTime.
     *
     * @return varchar
     */
    public function getUpdateTime();
    public function setUpdateTime($updateTime);

    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt();
    public function setCreatedAt($createdAt);
}
