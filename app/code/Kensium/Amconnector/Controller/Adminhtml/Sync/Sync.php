<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Sync;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Sync extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;

    /**
     * @var ResourceConnection|\Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Kensium\Amconnector\Helper\Data
     */
    protected $amconnectorHelper;
    /**
     * @var
     */
    protected $amconnectorCustomerHelper;
    /**
     * @var
     */
    protected $resource;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\CustomerAttribute
     */
    protected $customerAttribute;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param ScopeConfigInterface $scopeConfig
     * @param \Kensium\Amconnector\Helper\Data $amconnectorHelper
     * @param \Kensium\Amconnector\Helper\Customer $amconnectorCustomerHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\CustomerAttribute $customerAttribute
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\Filesystem\Io\File $file,
        ScopeConfigInterface $scopeConfig,
        \Kensium\Amconnector\Helper\Data $amconnectorHelper,
        \Kensium\Amconnector\Helper\Customer $amconnectorCustomerHelper,
        \Kensium\Amconnector\Model\ResourceModel\CustomerAttribute $customerAttribute,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->dir = $dir;
        $this->file = $file;
        $this->scopeConfig = $scopeConfig;
        $this->amconnectorHelper = $amconnectorHelper;
        $this->_resource = $resource;
        $this->amconnectorCustomerHelper = $amconnectorCustomerHelper;
        $this->customerAttribute = $customerAttribute;
        $this->resultPageFactory = $resultPageFactory;
        $this->resourceConfig = $resourceConfig;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $path = $this->getRequest()->getParam('path');
        $query = "update ".$this->resourceConfig->getTable('core_config_data')." set value = 0 where path ='".$path."flg'";
        try{
          $connection->query($query);
        }catch(Exception $e){
          echo $e->getMessage();
        }
    }
}
