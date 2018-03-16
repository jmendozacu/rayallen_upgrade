<?php
/**
 * @category   Amconnector
 * @package    Amconnector_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Button;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;
class DomainExportRequest extends Field
{
    /**
     * Path to block template
     */
    const BUTTON_TEMPLATE = 'system/config/button/domain_export_license.phtml';

    /**
     * Test Connection Button Label
     *
     * @var string
     */
    protected $_buttonLabel = 'Send License Request';

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
        $originalData = $element->getOriginalData();
        $buttonLabel = !empty($originalData['button_label']) ? $originalData['button_label'] : $this->_buttonLabel;
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl('amconnector/system_config/domainexportrequest'),
                'ajax_redirect_url'=> $this->_urlBuilder->getUrl('amconnector/system_config/exportdomainlicense'),
            ]
        );
        return $this->_toHtml();
    }
}