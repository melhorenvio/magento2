<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\Data\QuoteInterface;
use MelhorEnvio\Quote\Api\Data\QuoteInterfaceFactory;
use MelhorEnvio\Quote\Api\QuoteRepositoryInterface;
use MelhorEnvio\Quote\Controller\Adminhtml\BaseController;
use MelhorEnvio\Quote\Api\PackageRepositoryInterface;
use MelhorEnvio\Quote\Api\ShippingManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
/**
 * Class Resend
 * @package MelhorEnvio\Quote\Controller\Adminhtml\Quote
 */
class Resend extends BaseController
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var QuoteInterfaceFactory
     */
    private $quoteFactory;

    private $packageRepository;

    private $shippingManagement;

    private $orderRepository;
    /**
     * Constructor
     *
     * @param Context $context
     * @param QuoteRepositoryInterface $quoteRepository
     * @param QuoteInterfaceFactory $quoteFactory
     */
    public function __construct(
        Context $context,
        QuoteRepositoryInterface $quoteRepository,
        QuoteInterfaceFactory $quoteFactory,
        PackageRepositoryInterface $packageRepository,
        ShippingManagementInterface $shippingManagement,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->packageRepository = $packageRepository;
        $this->shippingManagement = $shippingManagement;
        $this->orderRepository = $orderRepository;

    }

    public function execute()
    {
        try {
            $quoteId = $this->getRequest()->getParam('quote_id');
            $origQuote = $this->quoteRepository->get($quoteId);
        } catch (LocalizedException $e) {
            return $this->redirectWithError(__('Não foi possível encontrar o envio'));
        }


        try {
            $order = $this->orderRepository->get($origQuote->getOrderId());
            foreach ($order->getShipmentsCollection() as $shipment){
                $shipment->delete();
            }
            $quoteReverse = 0;
            $this->shippingManagement->createShippingFromOrderResend($order,$quoteReverse);
        } catch (LocalizedException $e) {
            return $this->redirectWithError(__('Não foi possível gerar o novo Envio'));
        }

        return $this->redirectWithSuccess(__('Novo Envio gerado com sucesso'));
    }
}
