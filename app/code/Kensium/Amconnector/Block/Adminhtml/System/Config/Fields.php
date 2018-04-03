<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;

/**
 * Class Fields
 * @package Kensium\Amconnector\Block\Adminhtml\System\Config
 */
class Fields extends Field
{

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderScopeLabel(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '';
        if(is_object($this->_storeManager)){
            if ($element->getScope() && false == $this->_storeManager->isSingleStoreMode()) {
                $html .= ' data-config-scope="' . $element->getScopeLabel() . '"';
            }
        }else{
            if ($element->getScope()) {
                $html .= $element->getScopeLabel();
            }
        }

        $html .= '';
        return $html;
    }
}
