<?php

namespace MelhorEnvio\Quote\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Setup\Exception;
use MelhorEnvio\Quote\Model\Carrier\MelhorEnvio;

/**
 * Class Data
 * @package MelhorEnvio\Quote\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool) $this->getConfigData('active');
    }

    /**
     * @return bool
     */
    public function isSandbox(): bool
    {
        return (bool) $this->getConfigData('sandbox');
    }

    /**
     * @return string
     */
    public function getMelhorEnvioUrl()
    {
        $prefix = $this->isSandbox() ? 'sandbox.' : '';

        return 'https://' . $prefix . 'melhorenvio.com.br';
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->getConfigData('token');
    }

    /**
     * @return bool
     */
    public function alwaysSafe(): bool
    {
        return (bool) $this->getConfigData('quote/safe');
    }

    /**
     * @return bool
     */
    public function isReceipt(): bool
    {
        return (bool) $this->getConfigData('quote/receipt');
    }

    /**
     * @return bool
     */
    public function isCollect(): bool
    {
        return (bool) $this->getConfigData('quote/collect');
    }

    /**
     * @return bool
     */
    public function isOwnHand(): bool
    {
        return (bool) $this->getConfigData('quote/own_hand');
    }

    /**
     * @return bool
     */
    public function generateLog(): bool
    {
        return (bool) $this->getConfigData('log');
    }

    /**
     * @return bool
     */
    public function enableUrlDebug(): bool
    {
        return (bool) $this->getConfigData('url_debug');
    }

    /**
     * @return string
     */
    public function getServicesAvailable()
    {
        return $this->getConfigData('companies/services');
    }

    /**
     * @param $field
     * @param $scopeCode
     * @return mixed
     */
    public function getAddressFromConfigData($field, $scopeCode = null)
    {
        return $this->getConfigData(sprintf('from/%s', $field), $scopeCode);
    }

    /**
     * @param $field
     * @param $scopeCode
     * @return mixed
     */
    public function getConfigData($field, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'carriers/' . MelhorEnvio::CODE . '/' . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $state
     * @return string
     */
    public function getStateUf($state): string
    {
        $ufs = [
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amap??',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Cear??',
            'DF' => 'Distrito Federal',
            'ES' => 'Esp??rito Santo',
            'GO' => 'Goi??s',
            'MA' => 'Maranh??o',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Par??',
            'PB' => 'Para??ba',
            'PR' => 'Paran??',
            'PE' => 'Pernambuco',
            'PI' => 'Piau??',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rond??nia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'S??o Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins'
        ];

        return array_search($state, $ufs, true);
    }

    /**
     * @param $weight
     * @return float
     */
    public function getProductWeight($weight)
    {
        if (!is_null($weight)) {
            return $weight * $this->getConfigData('config_dev/weight_unit');
        }

        return $this->getConfigData('product/weight_default');
    }

    /**
     * @param $string
     * @return string|string[]|null
     */
    public function removeAccent($string)
    {
        $patterns = [
            'a' => '(??|??|??|??)',
            'e' => '(??|??|??|??)',
            'i' => '(??|??|??|??)',
            'o' => '(??|??|??|??)',
            'u' => '(??|??|??|??)',
            'A' => '(??|??|??|??)',
            'E' => '(??|??|??|??)',
            'I' => '(??|??|??|??)',
            'O' => '(??|??|??|??)',
            'U' => '(??|??|??|??)'
        ];
        $result = preg_replace(array_values($patterns), array_keys($patterns), $string);

        return ($result);
    }

    public function simpleRequest($method, $uri)
    {
        try {
            $client = new Client(['base_uri' => $this->getMelhorEnvioUrl()]);
            $result = $client->request($method, $uri, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => sprintf('Bearer %s', $this->getToken())
                ]
            ]);
            return json_decode($result->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage());
        }
    }
}
