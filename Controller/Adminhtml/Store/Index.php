<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml\Store;

use MelhorEnvio\Quote\Api\LoggerInterface;
use MelhorEnvio\Quote\Helper\Data as Helper;
use Magento\Checkout\Model\Session as CheckoutSession;

class Index extends \Magento\Backend\App\Action
{
    protected $_resultPageFactory;
    protected $_storesFactory;
    protected $helper;
    protected $logger;
    protected $storeFactory;
    protected $storeModel;
    protected $checkoutSession;

    /**
     * Index constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \MelhorEnvio\Quote\Model\Store\GridFactory $storeFactory
     * @param \MelhorEnvio\Quote\Model\Store\Grid $storeModel
     * @param CheckoutSession $checkoutSession
     * @param Helper $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \MelhorEnvio\Quote\Model\Store\GridFactory $storeFactory,
        \MelhorEnvio\Quote\Model\Store\Grid $storeModel,
        CheckoutSession $checkoutSession,
        Helper $helper,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->storeFactory = $storeFactory;
        $this->storeModel = $storeModel;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        $response = $this->helper->simpleRequest('GET', '/api/v2/me/companies');
        $this->checkoutSession->setQuoteId(
            $this->_request->getParam('quote_id')
        );
        $this->cleanTable();
        foreach ($response->data as $rowUnit) {
            $this->JsonToDataObject($rowUnit->id);
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('MelhorEnvio\Quote_Grid::grid_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Selecione a Origem'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Trezo_Grid::grid_list');
    }

    protected function JsonToDataObject($key)
    {

        $response = $this->helper->simpleRequest(
            'GET',
            "/api/v2/me/companies/{$key}/addresses"
        );

        foreach ($response->data as $row) {
            $store = $this->storeFactory->create();
            $store->setMelhorStorePostcode($row->postal_code);
            $store->setMelhorStoreName($row->label);
            $store->setMelhorStoreStreet($row->address);
            $store->setMelhorStoreNumber($row->number);
            $store->setMelhorStoreComplement($row->complement);
            $store->setMelhorStoreDistrict($row->district);
            $store->setMelhorStoreCity($row->city->city);
            $store->setMelhorStoreState($row->city->state->state);
            $store->setMelhorStoreKey($key);
            $store->save();
        }
    }

    public function cleanTable()
    {
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->storeModel->getResource()->getConnection();
        $tableName = $this->storeModel->getResource()->getMainTable();
        $connection->truncateTable($tableName);
    }
}
