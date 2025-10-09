<?php

namespace MelhorEnvio\Quote\Model\Cart;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use MelhorEnvio\Quote\Api\Data\PackageInterface;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;
use MelhorEnvio\Quote\Api\DataProviderInterface;
use MelhorEnvio\Quote\Helper\Data;
use MelhorEnvio\Quote\Model\Store\GridFactory as StoreFactory;

/**
 * Class DataProvider
 * @package MelhorEnvio\Quote\Model\Cart
 */
class DataProvider implements DataProviderInterface
{
    /**
     * @var PackageInterface
     */
    private $package;
    /**
     * @var QuoteInterface
     */
    private $shipping;
    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var OrderInterface|null
     */
    private $parentOrder = null;
    /**
     * @var OrderAddressRepositoryInterface
     */
    private $orderAddressRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var OrderAddressInterface
     */
    private $shippingAddress;
    /**
     * @var mixed
     */
    private $reverse;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;

    /**
     * @var \MelhorEnvio\Quote\Model\Store\GridFactory $store
     **/

    protected $store;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * DataProvider constructor.
     * @param Data $helperData
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderAddressRepositoryInterface $orderAddressRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        Data                                            $helperData,
        OrderRepositoryInterface                        $orderRepository,
        OrderAddressRepositoryInterface                 $orderAddressRepository,
        SearchCriteriaBuilder                           $searchCriteriaBuilder,
        StoreManagerInterface                           $storeManager,
        ScopeConfigInterface                            $scopeConfig,
        StoreFactory                                    $store,
        array                                           $data = []
    )
    {
        $this->productMetadata = $productMetadata;
        $this->helperData = $helperData;
        $this->orderRepository = $orderRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->store = $store;
        $this->package = $data['package'];
        $this->shipping = $data['shipping'];
        $this->reverse = $data['reverse'];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'service' => $this->shipping->getService(),
            'from' => $this->extractFromAddress($this->shipping),
            'to' => $this->extractToAddress(),
            'products' => $this->extractProducts(),
            'options' => [
                'receipt' => $this->helperData->isReceipt(),
                'own_hand' => $this->helperData->isOwnHand(),
                'collect' => $this->helperData->isCollect(),
                'non_commercial' => true,
                "platform" => "Magento" . $this->productMetadata->getVersion(),
                "tags" => [
                    "tag" => $this->shipping->getOrderId(),
                ]
            ]
        ];

        if ($this->reverse) {
            $data['options']['reverse'] = $this->reverse;
            $data['to'] = $this->extractFromAddress($this->shipping);
            $data['from'] = $this->extractToAddress();
        }

        if ($this->shipping->getNfKey()) {
            $data['options']['invoice'] = [
                'key' => $this->shipping->getNfKey()
            ];

            $data['options']['non_commercial'] = false;
        }

        $data['options']['insurance_value'] = $this->getInsuranceValue();

        if ($this->shipping->createTagsByVolumes()) {
            $data['package'] = $this->extractPackages();
        } else {
            $data['agency'] = $this->helperData->getConfigData('agency/jadlog_default');
            $data['volumes'] = $this->extractPackages();
        }

        return $data;
    }

    /**
     * @return array
     */
    private function extractFromAddress($shipping = null): array
    {
        $store = $this->store->create()->load($shipping->getOrigin());
        if ($shipping->getOrigin() !== null) {
            $data = [
                'name' => $store->getMelhorStoreName(),
                'phone' => $this->helperData->getAddressFromConfigData('telephone', $shipping->getOrigin()),
                'email' => $this->helperData->getAddressFromConfigData('email', $shipping->getOrigin()),
                'address' => $store->getMelhorStoreStreet(),
                'number' => $store->getMelhorStoreNumber(),
                'district' => $store->getMelhorStoreDistrict(),
                'city' => $store->getMelhorStoreCity(),
                'country_id' => $this->helperData->getAddressFromConfigData('country', $shipping->getOrigin()),
                'postal_code' => $store->getMelhorStorePostcode(),
            ];
        } else {
            $data = [
                'name' => $this->helperData->getAddressFromConfigData('name'),
                'phone' => $this->helperData->getAddressFromConfigData('telephone'),
                'email' => $this->helperData->getAddressFromConfigData('email'),
                'address' => $this->helperData->getAddressFromConfigData('street'),
                'number' => $this->helperData->getAddressFromConfigData('number'),
                'district' => $this->helperData->getAddressFromConfigData('district'),
                'city' => $this->helperData->getAddressFromConfigData('city'),
                'country_id' => $this->helperData->getAddressFromConfigData('country'),
                'postal_code' => $this->helperData->getAddressFromConfigData('postcode'),
            ];
        }

        $isCompany = (int)$this->helperData->getAddressFromConfigData('is_company');

        if ($this->helperData->getAddressFromConfigData('document') && !$isCompany) {
            $data['document'] = $this->helperData->getAddressFromConfigData('document');
        }

        if ($this->helperData->getAddressFromConfigData('company_document') && $isCompany) {
            $data['company_document'] = $this->helperData->getAddressFromConfigData('company_document');
        }

        if ($this->helperData->getAddressFromConfigData('state_register') && $isCompany) {
            $data['state_register'] = $this->helperData->getAddressFromConfigData('state_register');
        }

        if ($this->helperData->getAddressFromConfigData('cnae') && $isCompany) {
            $data['economic_activity_code'] = $this->helperData->getAddressFromConfigData('cnae');
        }

        if ($complement = $this->helperData->getAddressFromConfigData('complement')) {
            $data['complement'] = $complement;
        }

        if ($note = $this->helperData->getAddressFromConfigData('note')) {
            $data['note'] = $note;
        }

        return $data;
    }

    /**
     * @return array
     */
    private function extractToAddress(): array
    {
        $order = $this->getParentOrder();
        $shippingAddress = $this->getShippingAddress();
        $streetArr = $shippingAddress->getStreet();
        $cpfCnpj = $order->getCustomerTaxvat() ?: (string) $order->getBillingAddress()->getVatId();
        $cpfCnpj = preg_replace("/[^0-9]/", '', (string) $cpfCnpj);
        $data = [
            'name' => sprintf('%s %s', $shippingAddress->getFirstname(), $shippingAddress->getLastname()),
            'phone' => $shippingAddress->getTelephone(),
            'email' => $shippingAddress->getEmail(),
            'address' => $streetArr[0],
            'number' => $streetArr[1],
            'district' => (array_key_exists(2, $streetArr)) ? $streetArr[2] : '',
            'complement' => (array_key_exists(3, $streetArr)) ? $streetArr[3] : '',
            'city' => $shippingAddress->getCity(),
            'state_abbr' => $this->helperData->getStateUf($shippingAddress->getRegion()),
            'country_id' => $shippingAddress->getCountryId(),
            'postal_code' => $shippingAddress->getPostcode()
        ];

        if (strlen($cpfCnpj) <= 11) {
            $data['document'] = $cpfCnpj;
        } else {
            $data['company_document'] = $cpfCnpj;
        }

        return $data;
    }

    /**
     * @return OrderInterface
     */
    private function getParentOrder(): OrderInterface
    {
        if ($this->parentOrder == null) {
            $order = $this->orderRepository->get($this->shipping->getOrderId());
            $this->parentOrder = $order;
        }

        return $this->parentOrder;
    }

    /**
     * @return OrderAddressInterface
     */
    private function getShippingAddress(): OrderAddressInterface
    {
        if ($this->shippingAddress == null) {
            $searchCriteria = $this->searchCriteriaBuilder;
            $searchCriteria->addFilter('address_type', 'shipping');
            $searchCriteria->addFilter('parent_id', $this->shipping->getOrderId());

            $addressesList = $this->orderAddressRepository->getList($searchCriteria->create());
            $addresses = $addressesList->getItems();
            $this->shippingAddress = reset($addresses);
        }

        return $this->shippingAddress;
    }

    /**
     * @return array
     */
    private function extractProducts(): array
    {
        $items = [];
        $lastSkuPass = '';
        if (!$this->shipping->createTagsByVolumes()) {
            foreach ($this->getParentOrder()->getItems() as $item) {
                if ($lastSkuPass != $item->getSku()) {
                    $items[] = $this->getPackageItemData($item, $item->getQtyOrdered());
                }

                $lastSkuPass = $item->getSku();
            }
            return $items;
        }

        $package = json_decode($this->package->getPackages() ?? '', true);
        foreach ($package['products'] as $itemPkg) {
            foreach ($this->getParentOrder()->getItems() as $item) {
                if ($itemPkg['id'] == $item->getSku()) {
                    if ($lastSkuPass != $item->getSku()) {
                        $items[] = $this->getPackageItemData($item, $itemPkg['quantity']);
                    }
                }
                $lastSkuPass = $item->getSku();
            }
        }

        return $items;
    }

    /**
     * @param $item
     * @param $qty
     * @return array
     */
    private function getPackageItemData($item, $qty)
    {
        return [
            'name' => $item->getName(),
            'unitary_value' => floatval($item->getPrice()),
            'quantity' => $qty,
            'weight' => $this->helperData->getProductWeight($item->getWeight())
        ];
    }

    /**
     * @return array
     */
    private function extractPackages(): array
    {
        $data = [];

        $packages = $this->getPackageData();

        if ($this->shipping->createTagsByVolumes()) {
            $data = $packages['dimensions'];
            $data['weight'] = $packages['weight'];

            return $data;
        }

        foreach ($packages as $key => $item) {
            $item['dimensions']['weight'] = $item['weight'];
            $data[] = $item['dimensions'];
        }

        return $data;
    }

    /**
     * @return float
     */

    private function getInsuranceValue(): float
    {
        $packages = $this->getPackageData();

        //Insurance value for correios, loggi and J&T
        if ($this->shipping->createTagsByVolumes()) {
            return !$this->helperData->alwaysSafe() && $this->shipping->isCorreioService()
                ? 0.0
                : (float)$packages['insurance_value'];
        }

        $insurance = 0.0;
        foreach ($packages as $item) {
            $insurance += $item['insurance_value'];
        }
        return (float)$insurance;
    }

    /**
     * @return array
     */
    private function getPackageData(): array
    {
        $data = json_decode($this->package->getPackages() ?? '', true);

        return is_array($data) ? $data : [];
    }

    public function getStoreAddress()
    {
        return $this->shipping;
    }
}
