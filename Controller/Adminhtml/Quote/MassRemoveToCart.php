<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use MelhorEnvio\Quote\Api\ShippingManagementInterface;
use MelhorEnvio\Quote\Controller\Adminhtml\BaseController;
use MelhorEnvio\Quote\Model\ResourceModel\Quote\CollectionFactory;
use MelhorEnvio\Quote\Model\Services\RemoveToCartFactory;

/**
 * Class MassRemoveToCart
 * @package MelhorEnvio\Quote\Controller\Adminhtml\Quote
 */
class MassRemoveToCart extends BaseController
{
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var ShippingManagementInterface
     */
    private $shippingManagement;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ShippingManagementInterface $shippingManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ShippingManagementInterface $shippingManagement
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->shippingManagement = $shippingManagement;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
        } catch (LocalizedException $e) {
            return $this->redirectWithError(__('Fretes não localizado'));
        }

        foreach ($collection->getAllIds() as $shippingId) {
            try {
                $this->shippingManagement->removeShippingToCart($shippingId);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                    __('Não foi possível remover o frete %1 do carrinho', $shippingId)
                );
                continue;
            }

            $this->messageManager->addSuccessMessage(__('Frete %1 foi removido do carrinho', $shippingId));
        }

        return $this->redirect();
    }
}
