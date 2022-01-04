<?php

namespace MelhorEnvio\Quote\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;
use MelhorEnvio\Quote\Api\Data\QuoteInterfaceFactory;
use MelhorEnvio\Quote\Model\ResourceModel\Quote\Collection;

/**
 * Class Quote
 * @package MelhorEnvio\Quote\Model
 */
class Quote extends AbstractModel
{
    protected $dataObjectHelper;

    protected $quoteDataFactory;

    protected $_eventPrefix = 'melhorenvio_quote';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param QuoteInterfaceFactory $quoteDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\Quote $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        QuoteInterfaceFactory $quoteDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\Quote $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        $this->quoteDataFactory = $quoteDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve quote model with quote data
     * @return QuoteInterface
     */
    public function getDataModel()
    {
        $quoteData = $this->getData();

        $quoteDataObject = $this->quoteDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteDataObject,
            $quoteData,
            QuoteInterface::class
        );

        return $quoteDataObject;
    }
}
