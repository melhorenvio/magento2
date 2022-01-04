<?php

namespace MelhorEnvio\Quote\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use MelhorEnvio\Quote\Api\Data\PackageInterface;
use MelhorEnvio\Quote\Api\Data\PackageInterfaceFactory;
use MelhorEnvio\Quote\Model\ResourceModel\Package\Collection;

/**
 * Class Package
 * @package MelhorEnvio\Quote\Model
 */
class Package extends AbstractModel
{
    /**
     * @var PackageInterfaceFactory
     */
    protected $packageDataFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var string
     */
    protected $_eventPrefix = 'melhorenvio_package';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param PackageInterfaceFactory $packageDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\Package $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PackageInterfaceFactory $packageDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\Package $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        $this->packageDataFactory = $packageDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve package model with package data
     * @return PackageInterface
     */
    public function getDataModel()
    {
        $packageData = $this->getData();

        $packageDataObject = $this->packageDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $packageDataObject,
            $packageData,
            PackageInterface::class
        );

        return $packageDataObject;
    }
}
