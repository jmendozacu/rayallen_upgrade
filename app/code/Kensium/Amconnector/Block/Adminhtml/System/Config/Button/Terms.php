<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Button;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;
class Terms extends Field
{
    /**
     * Path to block template
     */
    const BUTTON_TEMPLATE = 'system/config/button/sendlicense.phtml';

    /**
     * Test Connection Button Label
     *
     * @var string
     */
    protected $_buttonLabel = 'Terms and Conditions';

    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::BUTTON_TEMPLATE);
        }
        return $this;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {

      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $storeManager= $objectManager->create('Magento\Store\Model\StoreManagerInterface');
      $baseUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC);
        $html = "
        <input type='checkbox' class='required-entry' id='terms' name='terms' checked> Terms and Conditions (<a href='javascript:void(0);' onclick=\"termsPopAction('".$baseUrl."')\">click here</a>)
        ";
        return $html;
    }
}
