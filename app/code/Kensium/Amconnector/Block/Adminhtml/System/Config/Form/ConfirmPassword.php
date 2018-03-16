<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

class ConfirmPassword extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = "
                <input  type='password' id='amconnectorcommon_amconnectoracucon_confirmPassword' name='groups[amconnectoracucon][fields][confirmPassword][value]'>
                <span id='errorConfirmPassword' style='color: red'></span>
            ";
        return $html ;
    }
}
