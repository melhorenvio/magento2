<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\PackageManagementInterface;
use MelhorEnvio\Quote\Api\ShippingManagementInterface;
use MelhorEnvio\Quote\Controller\Adminhtml\BaseController;
use MelhorEnvio\Quote\Model\Services\CheckoutFactory as ServiceCheckoutFactory;

/**
 * Class Checkout
 * @package MelhorEnvio\Quote\Controller\Adminhtml\Quote
 */
class Checkout extends BaseController
{
    /**
     * @var ServiceCheckoutFactory
     */
    private $serviceCheckoutFactory;
    /**
     * @var PackageManagementInterface
     */
    private $packageManagement;
    /**
     * @var ShippingManagementInterface
     */
    private $shippingManagement;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ShippingManagementInterface $shippingManagement
     * @param PackageManagementInterface $packageManagement
     * @param ServiceCheckoutFactory $serviceCheckoutFactory
     */
    public function __construct(
        Context $context,
        ShippingManagementInterface $shippingManagement,
        PackageManagementInterface $packageManagement,
        ServiceCheckoutFactory $serviceCheckoutFactory
    ) {
        parent::__construct($context);
        $this->serviceCheckoutFactory = $serviceCheckoutFactory;
        $this->packageManagement = $packageManagement;
        $this->shippingManagement = $shippingManagement;
    }

    public function execute()
    {
        try {
            $this->shippingManagement->invoiceAllShippingFromCart();
        } catch (LocalizedException $e) {
            return $this->redirectWithError(__($e->getMessage()));
        }

        return $this->redirectWithSuccess(__('Compra de fretes finalizada com sucesso!'));
    }
}
