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
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class IndividualCustomer extends \Magento\Backend\App\Action
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
    protected $customerHelper;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $resourceModelSync;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManagerInterface
     * @param \Kensium\Amconnector\Helper\Data $amconnectorHelper
     * @param \Kensium\Amconnector\Helper\Customer $customerHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem\Io\File $file,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManagerInterface,
        \Kensium\Amconnector\Helper\Data $amconnectorHelper,
        \Kensium\Amconnector\Helper\Customer $customerHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->dir = $dir;
        $this->file = $file;
        $this->scopeConfig = $scopeConfig;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->amconnectorHelper = $amconnectorHelper;
        $this->customerHelper = $customerHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->resourceModelSync = $resourceModelSync;
    }

    /**
     * Index action
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $customerId = $this->getRequest()->getParam('customer_id', NULL);
        $entity = 'customer';
        $storeCode = $this->storeManagerInterface->getStore()->getCode();
        $storeId = $this->storeManagerInterface->getStore($storeCode)->getId();
        $syncId = $this->resourceModelSync->getSyncId($entity, $storeId);
        try{
            $this->customerHelper->getCustomerSync('INDIVIDUAL', 'MANUAL', $syncId, NULL, $storeId,$flag='CUSTOMER',NULL, $customerId);
        }catch (Exception $e){
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
