<?php

namespace MelhorEnvio\Quote\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\CouldNotSaveException;
use MelhorEnvio\Quote\Api\Data\PackageInterface;
use MelhorEnvio\Quote\Model\ResourceModel\Package\CollectionFactory as PackageCollectionFactory;
use MelhorEnvio\Quote\Api\Data\PackageInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use MelhorEnvio\Quote\Api\PackageRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use MelhorEnvio\Quote\Api\Data\PackageSearchResultsInterfaceFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use MelhorEnvio\Quote\Model\ResourceModel\Package as ResourcePackage;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

/**
 * Class PackageRepository
 *
 * @package MelhorEnvio\Quote\Model
 */
class PackageRepository implements PackageRepositoryInterface
{
    /**
     * @var PackageSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * @var PackageInterfaceFactory
     */
    protected $dataPackageFactory;
    /**
     * @var PackageCollectionFactory
     */
    protected $packageCollectionFactory;
    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    /**
     * @var ResourcePackage
     */
    protected $resource;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var PackageFactory
     */
    protected $packageFactory;
    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param ResourcePackage $resource
     * @param PackageFactory $packageFactory
     * @param PackageInterfaceFactory $dataPackageFactory
     * @param PackageCollectionFactory $packageCollectionFactory
     * @param PackageSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ResourcePackage $resource,
        PackageFactory $packageFactory,
        PackageInterfaceFactory $dataPackageFactory,
        PackageCollectionFactory $packageCollectionFactory,
        PackageSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->resource = $resource;
        $this->packageFactory = $packageFactory;
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPackageFactory = $dataPackageFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        PackageInterface $package
    ) {
        $packageData = $this->extensibleDataObjectConverter->toNestedArray(
            $package,
            [],
            PackageInterface::class
        );

        $packageModel = $this->packageFactory->create()->setData($packageData);

        try {
            $this->resource->save($packageModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the package: %1',
                $exception->getMessage()
            ));
        }
        return $packageModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($packageId)
    {
        $package = $this->packageFactory->create();
        $this->resource->load($package, $packageId);
        if (!$package->getId()) {
            throw new NoSuchEntityException(__('Package with id "%1" does not exist.', $packageId));
        }
        return $package->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    ) {
        $collection = $this->packageCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        PackageInterface $package
    ) {
        try {
            $packageModel = $this->packageFactory->create();
            $this->resource->load($packageModel, $package->getPackageId());
            $this->resource->delete($packageModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Package: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($packageId)
    {
        return $this->delete($this->get($packageId));
    }

    /**
     * @inheritDoc
     */
    public function getListByParentShippingId(int $shippingId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(PackageInterface::QUOTE_ID, $shippingId)
            ->create();

        return $this->getList($searchCriteria);
    }

    /**
     * @param $code
     * @return PackageInterface|mixed
     * @throws NoSuchEntityException
     */
    public function getPackageCode($code)
    {
        $package = $this->packageFactory->create();
        $this->resource->load($package, $code, PackageInterface::CODE);
        if (!$package->getId()) {
            throw new NoSuchEntityException(__('Package with code "%1" does not exist.', $code));
        }
        return $package->getDataModel();
    }
}
