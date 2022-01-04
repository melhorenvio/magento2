<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Phrase;
use MelhorEnvio\Quote\Api\Data\HttpResponseInterface;

/**
 * Class BaseController
 * @package MelhorEnvio\Quote\Controller\Adminhtml
 */
abstract class BaseController extends Action
{
    /**
     * @param HttpResponseInterface $response
     */
    protected function extractErrorFromHttpResponse(HttpResponseInterface $response)
    {
        $data = $response->getBodyArray();
        if (array_key_exists('message', $data) && !empty($data['message'])) {
            $this->messageManager->addErrorMessage(__($data['message']));
        }
        if (array_key_exists('error', $data)) {
            $this->messageManager->addErrorMessage(__($data['error']));
        }
    }

    /**
     * @param Phrase $message
     * @return mixed
     */
    protected function redirectWithError(Phrase $message)
    {
        $this->messageManager->addErrorMessage($message);
        return $this->redirect();
    }

    /**
     * @param Phrase $message
     * @return mixed
     */
    protected function redirectWithSuccess(Phrase $message)
    {
        $this->messageManager->addSuccessMessage($message);
        return $this->redirect();
    }

    /**
     * @return mixed
     */
    protected function redirect()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('melhorenvio_quote/*/');
    }
}
