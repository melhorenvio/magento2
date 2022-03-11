<?php

namespace MelhorEnvio\Quote\Model;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\CouldNotSaveException;
use MelhorEnvio\Quote\Api\Data\InventoryReservationInterface;
use MelhorEnvio\Quote\Model\ResourceModel\InventoryReservation\CollectionFactory as InventoryReservationCollectionFactory;
use MelhorEnvio\Quote\Api\Data\InventoryReservationInterfaceFactory;
use MelhorEnvio\Quote\Api\InventoryReservationRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use MelhorEnvio\Quote\Api\Data\InventoryReservationSearchResultsInterfaceFactory;
use MelhorEnvio\Quote\Model\ResourceModel\InventoryReservation as ResourceInventoryReservation;

/**
 * Class InventoryReservationRepository
 *
 * @package MelhorEnvio\Quote\Model
 */
class InventoryReservationRepository implements InventoryReservationRepositoryInterface
{
    /**
     * @var InventoryReservationSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var InventoryReservationCollectionFactory
     */
    protected $inventoryReservationCollectionFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    /**
     * @var ResourceInventoryReservation
     */
    protected $resource;
    /**
     * @var InventoryReservationFactory
     */
    protected $inventoryReservationFactory;
    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceInventoryReservation $resource
     * @param InventoryReservationFactory $inventoryReservationFactory
     * @param InventoryReservationCollectionFactory $inventoryReservationCollectionFactory
     * @param InventoryReservationSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceInventoryReservation $resource,
        InventoryReservationFactory $inventoryReservationFactory,
        InventoryReservationCollectionFactory $inventoryReservationCollectionFactory,
        InventoryReservationSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->inventoryReservationFactory = $inventoryReservationFactory;
        $this->inventoryReservationCollectionFactory = $inventoryReservationCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * @param InventoryReservationInterface $inventoryReservation
     * @return InventoryReservationInterface
     * @throws CouldNotSaveException
     */
    public function save($inventoryReservation)
    {
        $inventoryReservationData = $this->extensibleDataObjectConverter->toNestedArray($inventoryReservation,[],InventoryReservationInterface::class);

        $inventoryReservationModel = $this->inventoryReservationFactory->create()->setData($inventoryReservationData);

        try{
            $this->resource->save($inventoryReservationModel);
        }catch (\Exception $exception){
            throw new CouldNotSaveException(__('Could not save the inventory: %1', $exception->getMessage()));
        }
        return $inventoryReservationModel->getDataModel();
    }

    /**
     * @param $reservation_id
     * @return InventoryReservationInterface
     * @throws NoSuchEntityException
     */
    public function get($reservation_id)
    {
        $inventoryReservation = $this->inventoryReservationFactory->create();
        $this->resource->load($inventoryReservation, $reservation_id);
        if (!$inventoryReservation->getReservationId()) {
            throw new NoSuchEntityException(__('Inventory Reservation with id "%1" does not exist.', $reservation_id));
        }
        return $inventoryReservation;
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return \MelhorEnvio\Quote\Api\Data\InventoryReservationSearchResultsInterface
     */
    public function getList($searchCriteria)
    {
        $collection = $this->inventoryReservationCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @param InventoryReservationInterface $inventoryReservation
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete($inventoryReservation)
    {
        try {
            $inventoryReservationModel = $this->inventoryReservationFactory->create();
            $this->resource->load($inventoryReservationModel, $inventoryReservation->getReservationId());
            $this->resource->delete($inventoryReservationModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Inventory Reservation: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }
}
