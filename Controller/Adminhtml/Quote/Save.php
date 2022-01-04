<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml\Quote;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use MelhorEnvio\Quote\Model\Quote;

/**
 * Class Save
 * @package MelhorEnvio\Quote\Controller\Adminhtml\Quote
 */
class Save extends Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('quote_id');
            $model = $this->_objectManager->create(Quote::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Quote no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Quote.'));
                $this->dataPersistor->clear('melhorenvio_quote_quote');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['quote_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Quote.'));
            }

            $this->dataPersistor->set('melhorenvio_quote_quote', $data);
            return $resultRedirect->setPath('*/*/edit', ['quote_id' => $this->getRequest()->getParam('quote_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
