<?php

namespace MelhorEnvio\Quote\Block\Adminhtml\Quote\Edit;

use Magento\Backend\Block\Widget\Context;

/**
 * Class GenericButton
 * @package MelhorEnvio\Quote\Block\Adminhtml\Quote\Edit
 */
abstract class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

   /**
    * Return model ID
    *
    * @return int|null
    */
   public function getModelId()
   {
        return $this->context->getRequest()->getParam('quote_id');
   }

   /**
    * Generate url by route and parameters
    *
    * @param   string $route
    * @param   array $params
    * @return  string
    */
   public function getUrl($route = '', $params = [])
   {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
   }
}
