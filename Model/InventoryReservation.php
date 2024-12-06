<?php
namespace MelhorEnvio\Quote\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use MelhorEnvio\Quote\Api\Data\InventoryReservationInterfaceFactory;
use MelhorEnvio\Quote\Model\ResourceModel\InventoryReservation\Collection;

class InventoryReservation extends AbstractModel
{
    const CACHE_TAG = 'inventory_reservation';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'inventory_reservation';

    protected $_inventoryReservationFactory;

    protected $_dataObjectHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ResourceModel\InventoryReservation $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        InventoryReservationInterfaceFactory $inventoryReservationFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\InventoryReservation $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        $this->_inventoryReservationFactory = $inventoryReservationFactory;
        $this->_dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MelhorEnvio\Quote\Model\ResourceModel\InventoryReservation');
    }

    /**
     * Return a unique id for the model.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
