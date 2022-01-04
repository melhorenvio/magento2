<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use MelhorEnvio\Quote\Controller\Adminhtml\Quote;

/**
 * Class Edit
 * @package MelhorEnvio\Quote\Controller\Adminhtml\Quote
 */
class Edit extends Quote
{
   /**
    * @var PageFactory
    */
   protected $resultPageFactory;

   /**
    * @param Context $context
    * @param Registry $coreRegistry
    * @param PageFactory $resultPageFactory
    */
   public function __construct(
       Context $context,
       Registry $coreRegistry,
       PageFactory $resultPageFactory
   ) {
       $this->resultPageFactory = $resultPageFactory;
       parent::__construct($context, $coreRegistry);
   }

   /**
    * Edit action
    *
    * @return ResultInterface
    */
   public function execute()
   {
        $id = $this->getRequest()->getParam('quote_id');
        $model = $this->_objectManager->create(\MelhorEnvio\Quote\Model\Quote::class);

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Quote no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

       $this->_coreRegistry->register('melhorenvio_quote_quote', $model);

       /** @var Page $resultPage */
       $resultPage = $this->resultPageFactory->create();
       $this->initPage($resultPage)->addBreadcrumb(__('Editar Frete'), __('Editar Frete'));
       $resultPage->getConfig()->getTitle()->prepend(__('Fretes'));
       $resultPage->getConfig()->getTitle()->prepend(__('Adicionar Chave da NFe - Frete %1', $model->getId()));

       return $resultPage;
   }
}