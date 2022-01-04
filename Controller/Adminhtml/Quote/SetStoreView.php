<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\QuoteRepositoryInterface;
use MelhorEnvio\Quote\Controller\Adminhtml\BaseController;
use MelhorEnvio\Quote\Model\QuoteFactory;
use MelhorEnvio\Quote\Model\Store\GridFactory as StoreFactory;

class SetStoreView extends BaseController
{
    private $quoteRepository;
    private $quoteFactory;
    protected $storeFactory;

    public function __construct(
        Context $context,
        QuoteRepositoryInterface $quoteRepository,
        QuoteFactory $quoteFactory,
        StoreFactory $storeFactory
    ) {
        parent::__construct($context);
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->storeFactory = $storeFactory;
    }

    public function execute()
    {
        try {
            $quoteId = $this->getRequest()->getParam('quote_id');
            $melhorStoreId = $this->getRequest()->getParam('melhor_store_id');

            $store = $this->storeFactory->create()->load($melhorStoreId);
            foreach ($store->getCollection() as $item) {
                $quote  = $this->quoteFactory->create()->load($quoteId);
                $quote->setOrigin($store->getMelhorStoreId());
                $quote->save();
            }
        } catch (LocalizedException $e) {
            return $this->redirectWithError(__('Não foi possível encontrar o envio'));
        }

        return $this->redirectWithSuccess(__('Origem gravada com sucesso'));
    }
}
