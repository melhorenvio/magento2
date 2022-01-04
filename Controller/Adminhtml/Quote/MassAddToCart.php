<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml\Quote;

use  Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use MelhorEnvio\Quote\Api\ShippingManagementInterface;
use MelhorEnvio\Quote\Controller\Adminhtml\BaseController;
use MelhorEnvio\Quote\Model\ResourceModel\Quote\CollectionFactory;

/**
 * Class MassAddToCart
 * @package MelhorEnvio\Quote\Controller\Adminhtml\Quote
 */
class MassAddToCart extends BaseController
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
        Context                     $context,
        Filter                      $filter,
        CollectionFactory           $collectionFactory,
        ShippingManagementInterface $shippingManagement
    )
    {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->shippingManagement = $shippingManagement;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
        } catch (LocalizedException $e) {
            return $this->redirectWithError(__('Fretes não localizados'));
        }

        foreach ($collection->getAllIds() as $shippingId) {
            try {
                $this->shippingManagement->addShippingToCart($shippingId);
            } catch (\Exception $e) {
                if (is_array($e->getMessage())) {
                    $errors = json_decode($e->getMessage(), true);
                    foreach ($errors as [$error]) {
                        $this->messageManager->addErrorMessage(
                            __($error)
                        );
                    }
                } else {
                    $this->messageManager->addErrorMessage(
                        __($e->getMessage())
                    );
                }
                $this->messageManager->addErrorMessage(
                    __('Não foi possível adicionar o frete %1 no carrinho', $shippingId)
                );
                continue;
            }

            $this->messageManager->addSuccessMessage(__('Frete %1 foi adicionado ao carrinho', $shippingId));
        }

        return $this->redirect();
    }
}
