<?php

namespace MelhorEnvio\Quote\Model;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use MelhorEnvio\Quote\Api\Data\PackageInterface;
use MelhorEnvio\Quote\Api\Data\PackageSearchResultsInterface;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;
use MelhorEnvio\Quote\Api\PackageManagementInterface;
use MelhorEnvio\Quote\Api\PackageRepositoryInterface;
use MelhorEnvio\Quote\Api\ResourceModel\Package\Collection;
use MelhorEnvio\Quote\Model\Cart\DataProviderFactory as CartDataProviderFactory;
use MelhorEnvio\Quote\Model\ResourceModel\Package\CollectionFactory as PackageCollectionFactory;
use MelhorEnvio\Quote\Model\Services\AddToCartFactory as ServiceAddToCartFactory;
use MelhorEnvio\Quote\Model\Services\CancelFactory as ServiceCancelFactory;
use MelhorEnvio\Quote\Model\Services\CheckoutFactory as ServiceCheckoutFactory;
use MelhorEnvio\Quote\Model\Services\RemoveToCartFactory as ServiceRemoveToCartFactory;
use MelhorEnvio\Quote\Model\Services\TagGenerateFactory as ServiceTagGenerateFactory;
use MelhorEnvio\Quote\Model\Services\TagPreviewFactory as ServiceTagPreviewFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;

/**
 * Class PackageManagement
 * @package MelhorEnvio\Quote\Model
 */
class PackageManagement implements PackageManagementInterface
{
    /**
     * @var ServiceAddToCartFactory
     */
    private $serviceAddToCartFactory;
    /**
     * @var CartDataProviderFactory
     */
    private $cartDataProviderFactory;
    /**
     * @var PackageRepositoryInterface
     */
    private $packageRepository;
    /**
     * @var ServiceRemoveToCartFactory
     */
    private $serviceRemoveToCartFactory;
    /**
     * @var PackageCollectionFactory
     */
    private $packageCollectionFactory;
    /**
     * @var ServiceCheckoutFactory
     */
    private $serviceCheckoutFactory;

    /**
     * @var
     */
    private $serviceTagGenerateFactory;

    /**
     * @var
     */
    private $messageManager;

    /**
     * @var
     */
    private $resultFactory;

    /**
     * @var
     */
    private $serviceTagPreviewFactory;

    private $serviceCancelFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * PackageManagement constructor.
     * @param CartDataProviderFactory $cartDataProviderFactory
     * @param ServiceAddToCartFactory $serviceAddToCartFactory
     * @param ServiceRemoveToCartFactory $serviceRemoveToCartFactory
     * @param PackageRepositoryInterface $packageRepository
     * @param PackageCollectionFactory $packageCollectionFactory
     * @param ServiceCheckoutFactory $serviceCheckoutFactory
     * @param ServiceTagGenerateFactory $serviceTagGenerateFactory
     * @param ServiceTagPreviewFactory $serviceTagPreviewFactory
     * @param ServiceCancelFactory $serviceCancelFactory
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        CartDataProviderFactory $cartDataProviderFactory,
        ServiceAddToCartFactory $serviceAddToCartFactory,
        ServiceRemoveToCartFactory $serviceRemoveToCartFactory,
        PackageRepositoryInterface $packageRepository,
        PackageCollectionFactory $packageCollectionFactory,
        ServiceCheckoutFactory $serviceCheckoutFactory,
        ServiceTagGenerateFactory $serviceTagGenerateFactory,
        ServiceTagPreviewFactory $serviceTagPreviewFactory,
        ServiceCancelFactory $serviceCancelFactory,
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->cartDataProviderFactory = $cartDataProviderFactory;
        $this->serviceAddToCartFactory = $serviceAddToCartFactory;
        $this->packageRepository = $packageRepository;
        $this->serviceRemoveToCartFactory = $serviceRemoveToCartFactory;
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->serviceCheckoutFactory = $serviceCheckoutFactory;
        $this->serviceTagGenerateFactory = $serviceTagGenerateFactory;
        $this->serviceTagPreviewFactory = $serviceTagPreviewFactory;
        $this->serviceCancelFactory = $serviceCancelFactory;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function addPackageToCart(PackageInterface $package, QuoteInterface $shipping)
    {
        $reverse = false;

        if ($shipping->getQuoteReverse() == 1) {
            $reverse = true;
        }

        $dataProvider = $this->cartDataProviderFactory->create(['data' => [
            'package' => $package,
            'shipping' => $shipping,
            'reverse'=> $reverse
        ]]);

        $result = $this->serviceAddToCartFactory->create([
            'data' => $dataProvider->getData()
        ])->doRequest();

        $data = $result->getBodyArray();

        if(isset($data['message'])){
            throw new LocalizedException(__($data['message']));
        }

        if (isset($data['errors'])) {
            throw new LocalizedException(__(json_encode($data['errors'])));
        }

        if (isset($data['error'])) {
            throw new LocalizedException(__($data['error']));
        }

        $package->setProtocol($data['protocol']);
        $package->setCode($data['id']);

        $this->packageRepository->save($package);

        return;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function removePackageToCart(PackageInterface $package)
    {
        $result = $this->serviceRemoveToCartFactory->create([
            'data' => [$package->getCode()]
        ])->doRequest();

        $package->setProtocol('');
        $package->setCode('');

        $this->packageRepository->save($package);
    }

    /**
     * @param PackageInterface $package
     * @return mixed
     */
    public function generateTagsPackage(PackageInterface $package)
    {
        $cartIds[] = $package->getCode();
        $response = $this->serviceTagGenerateFactory->create(
            ['data' => ['orders' => $cartIds]
            ]
        )->doRequest();

        $data = $response->getBodyArray();

        if ($response->getCode() != 200) {
            return $this->redirectWithError(__(json_encode($data)));
        }

        if (isset($data['generate_key'])) {
            $data = [end($data)];
        }

        $data = reset($data);
        if (array_key_exists('status', $data) && $data['status'] == true) {
            return $this->redirectWithSuccess(__($data['message']));
        }
        if (array_key_exists('status', $data) && $data['status'] == false) {
            return $this->redirectWithError(__($data['message']));
        }

        return $this->redirectWithError(__('Não foi possível gerar a Etiqueta'));
    }

    /**
     * @param $shippingId
     * @return mixed
     * @throws LocalizedException
     */
    public function previewTagPackage($shippingId)
    {
        $packageCollection = $this->packageRepository->getListByParentShippingId($shippingId);
        $cartIds = [];
        foreach ($packageCollection->getItems() as $package) {
            if ($package->getProtocol()) {
                $packageCode = $package->getCode();
                $cartIds[] = $packageCode;
            }
        }
        $response = $this->serviceTagPreviewFactory->create(
            ['data' => ['orders' => $cartIds]]
        )->doRequest();

        $data = $response->getBodyArray();

        if ($response->getCode() != 200) {
            if (array_key_exists('error', $data)) {
                return $this->redirectWithError(__($data['error']));
            }
            $this->redirectWithError(__('Não foi possível visualizar a Etiqueta'));
        }
        if (array_key_exists('url', $data)) {
            return $data['url'];
        }

        return $this->redirectWithError(__('Não foi possível visualizar a Etiqueta'));
    }

    /**
     * @param PackageInterface $package
     * @return mixed
     * @throws LocalizedException
     */
    public function cancelTagsPackage(PackageInterface $package)
    {
        $response = $this->serviceCancelFactory->create(['data' => [
            'order' => [
                'id' => $package->getCode(),
                'reason_id' => 2,
                'description' => __('Cancelado através do Magento')
            ]
        ]])->doRequest();

        $data = $response->getBodyArray();

        if ($response->getCode() != 200) {
            if (array_key_exists('message', $data)) {
                return $this->redirectWithError(__($data['message']));
            }

            $this->redirectWithError(__('Não foi possível cancelar o fretea'));
        }

        $data = reset($data);

        if ($data) {
            if (array_key_exists('canceled', $data) && $data['canceled'] == true) {
                return $this->redirectWithSuccess(__('Frete cancelado.'));
            }
            if (array_key_exists('canceled', $data) && $data['canceled'] == false) {
                return $this->redirectWithError(__('Não foi possivel cancelar o frete.'));
            }
        }

        return $this->redirectWithError(__('Não foi possível cancelar a Etiqueta'));
    }

    /**
     * @inheritDoc
     */
    public function getPackagesAvailableToInvoice()
    {
        $collection = $this->packageCollectionFactory->create();
        $collection->addFieldToFilter('code', ['notnull' => true]);

        return $collection->getItems();
    }

    /**
     * @return PackageSearchResultsInterface|Collection
     * @throws LocalizedException
     */
    public function getPackagesToInvoiceCollection()
    {
        $filter = $this->filterBuilder
            ->setField(PackageInterface::CODE)
            ->setConditionType('notnull')
            ->setValue(true)
            ->create();
        $this->searchCriteriaBuilder->addFilters([$filter]);

        $searchCriteria = $this->searchCriteriaBuilder->create();

        return $this->packageRepository->getList($searchCriteria);
    }

    /**
     * @return mixed
     */
    protected function redirect()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('melhorenvio_quote/*/');
    }

    /**
     * @param Phrase $message
     * @return mixed
     */
    protected function redirectWithError(Phrase $message)
    {
        $this->messageManager->addErrorMessage($message);
        return $this->redirect();
    }

    /**
     * @param Phrase $message
     * @return mixed
     */
    protected function redirectWithSuccess(Phrase $message)
    {
        $this->messageManager->addSuccessMessage($message);
        return $this->redirect();
    }

    /**
     * @param $package
     * @throws LocalizedException
     */
    public function removePackageRepository($package)
    {
        try {
            $package->setProtocol('');
            $package->setCode('');

            $this->packageRepository->save($package);
        } catch (\Exception $e) {
            throw new LocalizedException(__('Não foi possível remover o pacote.'));
        }
    }

    /**
     * @param $code
     * @return bool
     * @throws LocalizedException
     */
    public function removePackageToAPI($code): bool
    {
        try {
            $this->serviceRemoveToCartFactory->create([
                'data' => [$code]
            ])->doRequest();
        } catch (\Exception $e) {

        }

        return true;
    }
}
