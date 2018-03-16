<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Help\Block\Adminhtml\Support;

use Magento\Backend\Block\Widget\Form\Container;

class Requirements extends \Magento\Backend\Block\Widget\Form
{


    /**
     * @var \Kensium\Help\Helper\Data
     */
    protected $helper;
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Help\Helper\Data $helper,
        $data = []
    )
    {
        parent::__construct($context, []);
        $this->helper = $helper;
        $this->setId('developmentInspectionRequirements');
        $this->setTemplate('requirements.phtml');
    }

    protected function _beforeToHtml()
    {
        $this->requirements = $this->helper->getRequirementsInfo();
        return parent::_beforeToHtml();
    }
}