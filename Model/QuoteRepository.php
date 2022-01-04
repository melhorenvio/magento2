<?php

namespace MelhorEnvio\Quote\Model;

use Exception;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;
use MelhorEnvio\Quote\Api\Data\QuoteInterfaceFactory;
use MelhorEnvio\Quote\Api\Data\QuoteSearchResultsInterfaceFactory;
use MelhorEnvio\Quote\Api\QuoteRepositoryInterface;
use MelhorEnvio\Quote\Model\ResourceModel\Quote as ResourceQuote;
use MelhorEnvio\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;

/**
 * Class QuoteRepository
 * @package MelhorEnvio\Quote\Model
 */
class QuoteRepository implements QuoteRepositoryInterface
{
    protected $dataObjectHelper;

    protected $dataQuoteFactory;

    private $storeManager;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    protected $quoteCollectionFactory;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $quoteFactory;

    /**
     * @param ResourceQuote $resource
     * @param QuoteFactory $quoteFactory
     * @param QuoteInterfaceFactory $dataQuoteFactory
     * @param QuoteCollectionFactory $quoteCollectionFactory
     * @param QuoteSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceQuote $resource,
        QuoteFactory $quoteFactory,
        QuoteInterfaceFactory $dataQuoteFactory,
        QuoteCollectionFactory $quoteCollectionFactory,
        QuoteSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->quoteFactory = $quoteFactory;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataQuoteFactory = $dataQuoteFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        QuoteInterface $quote
    ) {
        $quoteData = $this->extensibleDataObjectConverter->toNestedArray(
            $quote,
            [],
            QuoteInterface::class
        );

        $quoteModel = $this->quoteFactory->create()->setData($quoteData);

        try {
            $this->resource->save($quoteModel);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the quote: %1',
                $exception->getMessage()
            ));
        }
        return $quoteModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($quoteId)
    {
        $quote = $this->quoteFactory->create();
        $this->resource->load($quote, $quoteId);
        if (!$quote->getId()) {
            throw new NoSuchEntityException(__('Quote with id "%1" does not exist.', $quoteId));
        }
        return $quote->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getByCartId($cartId)
    {
        $quote = $this->quoteFactory->create();
        $this->resource->load($quote, $cartId, QuoteInterface::QUOTE_ID);
        if (!$quote->getId()) {
            throw new NoSuchEntityException(__('Quote with cartId "%1" does not exist.', $cartId));
        }
        return $quote->getDataModel();
    }

    /**
     * @param $orderId
     * @return bool|int
     */
    public function hasRecordAssociatedWithTheOrderId($orderId)
    {
        try {
            $quote = $this->quoteFactory->create();
            $this->resource->load($quote, $orderId, QuoteInterface::ORDER_ID);

            return $quote->getId();
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    ) {
        $collection = $this->quoteCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            QuoteInterface::class
        );

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
        QuoteInterface $quote
    ) {
        try {
            $quoteModel = $this->quoteFactory->create();
            $this->resource->load($quoteModel, $quote->getQuoteId());
            $this->resource->delete($quoteModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Quote: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($quoteId)
    {
        return $this->delete($this->get($quoteId));
    }
}
