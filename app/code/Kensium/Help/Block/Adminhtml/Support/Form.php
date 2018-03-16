<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Help\Block\Adminhtml\Support;

use Magento\Backend\Block\Widget\Form\Container;
use Kensium\Help\Controller\Adminhtml\Support;

class Form extends \Magento\Backend\Block\Widget\Form
{
    protected $_template = 'support.phtml';
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        $data = []
    ) {
        parent::__construct($context, []);
        $this->setId('supportGeneralForm');
        $this->setTemplate('support.phtml');
    }

    protected function _beforeToHtml()
    {
        $this->isFromError = $this->getRequest()->getParam('error') === 'true';

        $cronInfoBlock = $this->getLayout()->createBlock('Kensium\Help\Block\Adminhtml\Support\Cron','',array('is_support_mode' => true));
        $this->setChild('cron_info', $cronInfoBlock);

        $systemRequirementsBlock = $this->getLayout()->createBlock('Kensium\Help\Block\Adminhtml\Support\Requirements','',array('is_support_mode' => true));
        $this->setChild('system_requirements', $systemRequirementsBlock);

        return parent::_beforeToHtml();
    }
}