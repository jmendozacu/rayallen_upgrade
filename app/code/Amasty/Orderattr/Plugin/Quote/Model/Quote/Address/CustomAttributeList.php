<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Plugin\Quote\Model\Quote\Address;

class CustomAttributeList
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    private $attribute;

    /**
     * CustomAttributeList constructor.
     * @param \Magento\Eav\Model\Entity\Attribute $attributes
     */
    public function __construct(
        \Amasty\Orderattr\Model\ResourceModel\Eav\Attribute $attribute
    ) {
        $this->attribute = $attribute;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\CustomAttributeList $object
     * @param array $result
     * @return array
     */
    public function afterGetAttributes($object, $result)
    {
        $codes = $this->attribute->getOrderAttributesCodes();
        $attributes = [];
        foreach ($codes as $code) {
            $data[$code] = $this->attribute->loadOrderAttributeByCode($code);
        }
        return array_merge($result, $attributes);
    }
}
