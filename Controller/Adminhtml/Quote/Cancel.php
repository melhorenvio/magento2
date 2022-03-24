<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Model\Convert\Order as ConvertOrder;
use MelhorEnvio\Quote\Api\InventoryReservationRepositoryInterface;
use MelhorEnvio\Quote\Api\QuoteRepositoryInterface;
use MelhorEnvio\Quote\Controller\Adminhtml\BaseController;
use MelhorEnvio\Quote\Api\ShippingManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;

/**
 * Class Cancel
 * @package MelhorEnvio\Quote\Controller\Adminhtml\Quote
 */
class Cancel extends BaseController
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var ShippingManagementInterface
     */
    private $shippingManagement;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SourceItemInterfaceFactory
     */
    private $sourceItemFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepositoryInterface;

    /**
     * @var StockItemRepository
     */
    private $stockItemRepository;

    /**
     * @var SourceItemsSaveInterface
     */
    private $sourceItemsSaveInterface;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var ConvertOrder
     */
    private $convertOrder;

    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItemRepository;

    /**
     * @var InventoryReservationRepositoryInterface
     */
    private $inventoryReservationRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Cancel constructor.
     * @param Action\Context $context
     * @param QuoteRepositoryInterface $quoteRepository
     * @param ShippingManagementInterface $shippingManagement
     */
    public function __construct(
        Action\Context              $context,
        QuoteRepositoryInterface    $quoteRepository,
        ShippingManagementInterface $shippingManagement,
        OrderRepositoryInterface    $orderRepository,
        SourceItemInterfaceFactory  $sourceItemFactory,
        ProductRepositoryInterface  $productRepositoryInterface,
        SourceItemsSaveInterface    $sourceItemsSaveInterface,
        StockItemRepository         $stockItemRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ConvertOrder               $convertOrder,
        OrderItemRepositoryInterface $orderItemRepository,
        InventoryReservationRepositoryInterface $inventoryReservationRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        parent::__construct($context);
        $this->quoteRepository = $quoteRepository;
        $this->shippingManagement = $shippingManagement;
        $this->orderRepository = $orderRepository;
        $this->sourceItemFactory = $sourceItemFactory;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->stockItemRepository = $stockItemRepository;
        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
        $this->shipmentRepository = $shipmentRepository;
        $this->convertOrder = $convertOrder;
        $this->orderItemRepository = $orderItemRepository;
        $this->inventoryReservationRepository = $inventoryReservationRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function execute()
    {
        $shippingId = $this->getRequest()->getParam('quote_id');
        $orderId = $this->getRequest()->getParam('order_id');

        if (!$this->getRequest()->getParam('quote_id')) {
            return $this->redirectWithError(__('Não foi possível encontrar a etiqueta'));
        }

        try {
            $order = $this->orderRepository->get($orderId);
            $quote = $this->quoteRepository->get($shippingId);

            if($quote->getStatus() == 'posted' || $quote->getStatus() == 'released') {
                $searchCriteria = $this->searchCriteriaBuilder;
                $searchCriteria->addFilter('metadata', '{"event_type":"shipment_created","object_type":"order","object_id":"' . $orderId . '","object_increment_id":"' . $order->getIncrementId() . '"}');

                $inventoryReservationList = $this->inventoryReservationRepository->getList($searchCriteria->create());
                $inventoryReservationProducts = $inventoryReservationList->getItems();

                foreach ($inventoryReservationProducts as $inventoryReservation) {
                    $this->inventoryReservationRepository->delete($inventoryReservation);
                }

                $items = $order->getItems();
                foreach ($items as $item) {
                    $itemSku = $item->getSku();
                    $product = $this->productRepositoryInterface->get($itemSku);
                    $qtdShipped = $item->getQtyShipped();
                    $qtdAtual = $this->stockItemRepository->get($product->getId())->getQty();
                    $qtdTotal = $qtdShipped + $qtdAtual;
                    $item->setQtyShipped(0);

                    $this->orderItemRepository->save($item);
                    $this->orderRepository->save($order);

                    if ($qtdShipped) {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode('default');
                        $sourceItem->setSku($itemSku);
                        $sourceItem->setQuantity($qtdTotal);
                        $sourceItem->setStatus(1);

                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
            }
            $this->shippingManagement->cancelTags($shippingId);

        } catch (LocalizedException $e) {
            return $this->redirectWithError(__('Não foi possível cancelar o frete'. $e));
        }

        return $this->redirect();
    }
}
