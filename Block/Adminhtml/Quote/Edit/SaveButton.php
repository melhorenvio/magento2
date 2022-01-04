<?php

namespace MelhorEnvio\Quote\Block\Adminhtml\Quote\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 * @package MelhorEnvio\Quote\Block\Adminhtml\Quote\Edit
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{

    /**
    * @return array
    */
    public function getButtonData()
    {
        return [
            'label' => __('Save Quote'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
