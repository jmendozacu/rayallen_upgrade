<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Password extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = "
                <input  type='password' id='amconnectorcommon_amconnectoracucon_password' name='groups[amconnectoracucon][fields][password][value]'>
            ";
        return $html ;
    }
}
