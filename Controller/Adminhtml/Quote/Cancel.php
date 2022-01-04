<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;
use MelhorEnvio\Quote\Api\QuoteRepositoryInterface;
use MelhorEnvio\Quote\Controller\Adminhtml\BaseController;
use MelhorEnvio\Quote\Api\ShippingManagementInterface;

/**
 * Class Cancel
 * @package MelhorEnvio\Quote\Controller\Adminhtml\Quote
 */
class Cancel extends BaseController
{
    /**
     * @var CancelFactory
     */
    private $cancelFactory;
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    private $shippingManagement;

    /**
     * Cancel constructor.
     * @param Action\Context $context
     * @param CancelFactory $cancelFactory
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        Action\Context $context,
        QuoteRepositoryInterface $quoteRepository,
        ShippingManagementInterface $shippingManagement
    ) {
        parent::__construct($context);
        $this->quoteRepository = $quoteRepository;
        $this->shippingManagement = $shippingManagement;
    }

    public function execute()
    {
        $shippingId = $this->getRequest()->getParam('quote_id');
        if (!$this->getRequest()->getParam('quote_id')) {
            return $this->redirectWithError(__('Não foi possível encontrar a etiqueta'));
        }

        try {
            $this->shippingManagement->cancelTags($shippingId);
        } catch (LocalizedException $e) {
            return $this->redirectWithError(__('Não foi possível cancelar o frete'));
        }

        return $this->redirect();
    }
}
