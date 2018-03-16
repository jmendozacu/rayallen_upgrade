<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Help\Block\Adminhtml\Support;

class Cron extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var
     */
    protected $dir;

    /**
     * @var
     */
    protected $storeManager;

    /**
     * @var \Kensium\Help\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Kensium\Help\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Help\Helper\Data $helper,
        $data = []
    ) {
        parent::__construct($context, []);
        $this->helper = $helper;
        $this->_storeManager = $context->getStoreManager();
        $this->setId('developmentInspectionCron');
        $this->setTemplate('cron.phtml');
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->cronLastRunTime = 'N/A';
        $this->cronIsNotWorking = false;
        $this->cronPhp = 'php bin/magento cron:run';

        $baseUrl = $this->helper->getBaseUrl();
        $this->cronGet = 'php bin/magento cron:run';

        return parent::_beforeToHtml();
    }

}
