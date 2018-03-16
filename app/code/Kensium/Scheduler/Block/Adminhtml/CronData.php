<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Scheduler\Block\Adminhtml;
class CronData extends \Magento\Framework\View\Element\Template
{
    protected $_config;

    public function __construct( \Magento\Framework\View\Element\Template\Context $context,\Magento\Cron\Model\ConfigInterface $config, $data = array())
    {
        parent::__construct($context, $data = array());
        $this->_config = $config;
        $jobGroupsRoot = $this->_config->getJobs();
        $data = $this->_request->getParam('group');
       // die(print_r($jobGroupsRoot));
    }
    
    function getCronData()
    {
        //die('yes the method is called here');
        return $this->_config->getJobs();
    }
}
