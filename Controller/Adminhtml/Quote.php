<?php

namespace MelhorEnvio\Quote\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;

/**
 * Class Quote
 * @package MelhorEnvio\Quote\Controller\Adminhtml
 */
abstract class Quote extends Action
{
    const ADMIN_RESOURCE = 'MelhorEnvio_Quote::top_level';

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Melhor Envio'), __('Melhor Envio'))
            ->addBreadcrumb(__('Cotação'), __('Cotação'));
        return $resultPage;
    }
}
