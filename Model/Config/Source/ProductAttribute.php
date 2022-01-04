<?php

namespace MelhorEnvio\Quote\Model\Config\Source;

use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ProductAttribute
 * @package MelhorEnvio\Quote\Model\Config\Source
 */
class ProductAttribute implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $objectManager = ObjectManager::getInstance();
        $collection = $objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection')
            ->setAttributeSetFilter(4);

        $collection->getSelect()->order('frontend_label');

        $result = [];

        foreach ($collection as $child) {
            $result[] = ['value' => $child->getAttributeCode(), 'label' => $child->getFrontendLabel()];
        }

        return $result;
    }
}
