<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Button;

class AcumaticaBaseData extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Path to block template
     */
    const ACUMATICA_BASE_DATA_TEMPLATE = 'system/config/button/acumaticaBaseData.phtml';

    /**
     * Test Connection Button Label
     *
     * @var string
     */
    protected $_acumaticaBaseDataButtonLabel = 'Reload Acumatica Base Data';

    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::ACUMATICA_BASE_DATA_TEMPLATE);
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

        $acumatciaBaseUrl = $this->_urlBuilder->getUrl('amconnector/sync/acumaticaBaseData');
        $logViewUrl = $this->_urlBuilder->getUrl('amconnector/log/acumaticaBaseData');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager= $objectManager->create('Magento\Store\Model\StoreManagerInterface');
        $baseUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC);
        $ajaxUrl = $acumatciaBaseUrl.'||'.$logViewUrl."||".$baseUrl;
        $originalData = $element->getOriginalData();
        $buttonLabel = !empty($originalData['button_label']) ? $originalData['button_label'] : $this->_acumaticaBaseDataButtonLabel;
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $ajaxUrl,
            ]
        );

        return $this->_toHtml();
    }
}