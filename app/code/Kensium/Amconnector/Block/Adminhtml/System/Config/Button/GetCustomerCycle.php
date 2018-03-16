<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Button;

class GetCustomerCycle extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Path to block template
     */
    const GET_CUSTOMER_CYCLE_DATA_TEMPLATE = 'system/config/button/getCustomerCycle.phtml';

    /**
     * Get Customer Cycle Button Label
     *
     * @var string
     */
    protected $_customerCycleButtonLabel = 'Update Customer Statement Cycle Data';

    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::GET_CUSTOMER_CYCLE_DATA_TEMPLATE);
        }
        return $this;
    }

    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel = !empty($originalData['button_label']) ? $originalData['button_label'] : $this->_customerCycleButtonLabel;
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl('amconnector/system_config/getCustomerCycle'),
            ]
        );
        return $this->_toHtml();
    }
}