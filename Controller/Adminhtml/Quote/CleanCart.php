<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MelhorEnvio\Quote\Api\PackageRepositoryInterface;
use MelhorEnvio\Quote\Controller\Adminhtml\BaseController;
use MelhorEnvio\Quote\Model\Services\CartFactory;
use MelhorEnvio\Quote\Model\Services\RemoveToCartFactory;
use MelhorEnvio\Quote\Api\ShippingManagementInterface;

/**
 * Class CleanCart
 * @package MelhorEnvio\Quote\Controller\Adminhtml\Quote
 */
class CleanCart extends BaseController
{
    /**
     * @var CartFactory
     */
    private $cartFactory;
    /**
     * @var RemoveToCartFactory
     */
    private $removeToCartFactory;
    /**
     * @var PackageRepositoryInterface
     */
    private $packageRepository;

    /**
     * @var ShippingManagementInterface
     */
    private $shippingManagement;

    /**
     * CleanCart constructor.
     * @param Context $context
     * @param CartFactory $cartFactory
     * @param RemoveToCartFactory $removeToCartFactory
     * @param PackageRepositoryInterface $packageRepository
     * @param ShippingManagementInterface $shippingManagement
     */
    public function __construct(
        Action\Context $context,
        CartFactory $cartFactory,
        RemoveToCartFactory $removeToCartFactory,
        PackageRepositoryInterface $packageRepository,
        ShippingManagementInterface $shippingManagement
    ) {
        parent::__construct($context);
        $this->cartFactory = $cartFactory;
        $this->removeToCartFactory = $removeToCartFactory;
        $this->packageRepository = $packageRepository;
        $this->shippingManagement = $shippingManagement;
    }

    public function execute()
    {
        $cartService = $this->cartFactory->create();
        $result = $cartService->doRequest();
        if ($result->getCode() != 200) {
            $this->extractErrorFromHttpResponse($result);
            return $this->redirectWithError(__('Não foi possível limpar o carrinho'));
        }

        $data = $result->getBodyArray();
        if ($data['total'] < 1) {
            return $this->redirectWithError(__('Não há fretes no carrinho'));
        }

        foreach ($data['data'] as $item) {
            $code = $item['id'];
            $result = $this->removeToCartFactory->create(['data' => [$code]])->doRequest();
            if ($result->getCode() != 204) {
                $this->extractErrorFromHttpResponse($result);
                continue;
            }

            $shipping = $this->packageRepository->getPackageCode($code);
            $quoteId = $shipping->getQuoteId();
            $this->removeToCart($quoteId);

        }

        return $this->redirectWithSuccess(__('Fretes removidos do carrinho com sucesso'));
    }

    /**
     * @param $quoteId
     * @return bool
     */
    public function removeToCart($quoteId)
    {
        try {
            $this->shippingManagement->removeShippingToCart($quoteId);
        } catch (LocalizedException $e) {
            $this->redirectWithError(__('Não foi possível limpar o carrinho'));
        }

        return true;
    }
}
