<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use MelhorEnvio\Quote\Api\ShippingManagementInterface;
use MelhorEnvio\Quote\Controller\Adminhtml\BaseController;
use MelhorEnvio\Quote\Model\ResourceModel\Quote\CollectionFactory;

/**
 * Class MassTagPreview
 * @package MelhorEnvio\Quote\Controller\Adminhtml\Quote
 */
class MassTagPreview extends BaseController
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
     * @param QuoteRepositoryInterface $quoteRepository
     * @param TagGenerateFactory $tagGenerateFactory
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

    /**
     * @return mixed
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
                $previewTag = $this->shippingManagement->previewTags($shippingId);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                    __('Nao foi possivel gerar a etiqueta do pedido %1 ', $shippingId)
                );
                continue;
            }
            if (is_string($previewTag)) {
                return $this->_redirect($previewTag);
            } else {
                return $this->redirect();
            }
        }
    }
}
