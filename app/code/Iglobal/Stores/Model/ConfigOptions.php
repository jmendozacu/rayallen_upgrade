<?php

namespace Iglobal\Stores\Model;


class ConfigOptions
{
    /**
     * Attribute collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $_attributeCollectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->_attributeCollectionFactory->create()->load();
        $options = array(array('value'=> null, 'label'=> '- Not mapped -' ));
        foreach($collection as $attr) {
            if($attr->getIsVisible()) {
                $options[] = array('value' => $attr->getAttributeCode(), 'label' => $attr->getFrontendLabel());
            }
        }
        return $options;
    }
}