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
use Symfony\Component\Config\Definition\Exception\Exception;

class TestConnection extends \Magento\Backend\App\Action
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
    protected $resultPageFactory;

    /**
     * @var
     */
    protected $testConnectionHelper;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $resourceModelSync;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param ScopeConfigInterface $scopeConfig
     * @param \Kensium\Amconnector\Helper\TestConnection $testConnectionHelper
     * @param \Kensium\Amconnector\Helper\Data $amconnectorHelper
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem\Io\File $file,
        ScopeConfigInterface $scopeConfig,
        \Kensium\Amconnector\Helper\TestConnection $testConnectionHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Kensium\Amconnector\Helper\Data $amconnectorHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->dir = $dir;
        $this->file = $file;
        $this->scopeConfig = $scopeConfig;
        $this->amconnectorHelper = $amconnectorHelper;
        $this->_storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->resourceModelSync = $resourceModelSync;
        $this->testConnectionHelper = $testConnectionHelper;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     *
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store', NULL);
        if($storeId == 0 || $storeId == ''){
            $storeId = 1;
        }
        $serverUrl = $this->getRequest()->getParam('serverUrl', NULL);
        $userName = $this->getRequest()->getParam('userName', NULL);
        $password = $this->getRequest()->getParam('password', NULL);
        $confirmPassword = $this->getRequest()->getParam('confirmPassword', NULL);
        $company = $this->getRequest()->getParam('company', NULL);
        $syncDirectory = BP . "/var/amconnector/";
        $lockDirectory = $syncDirectory . "lock/";
        $this->file->checkAndCreateFolder($lockDirectory);
        $entity = "testConnection";
        $lockFile = $lockDirectory . $entity . ".lock";
        $backGroundSync = $this->resourceModelSync->getDataFromCoreConfig('amconnectorsync/background_sync/background_sync',NULL,NULL);
        if ($backGroundSync) {
            if(empty($serverUrl))
                $serverUrl = "NULL";
            if(empty($userName))
                $userName = "NULL";
            if(empty($password))
                $password = "NULL";
            if(empty($confirmPassword))
                $confirmPassword = "NULL";
            if(empty($company))
                $company = "NULL";

            $isLock = $this->amconnectorHelper->chkforDuplicateJob($entity);
            if ($isLock) {
                $this->messageManager->addError("Test connection already running");
            } else {
                try{
		    exec("php " . BP . "/bin/magento kensium:sync testConnection ".$serverUrl." '".$userName."' '".$password."' '".$confirmPassword."' '".$company."' $storeId > /dev/null & 1 & echo $!", $out);
                    if (isset($out[0])) {
                        $pid = $out[0] - 1;
                        file_put_contents($lockFile, $pid . "\n");
                    }
                }catch (Exception $e){
                    echo $e->getMessage();
                }
            }
        } else {
            $this->testConnectionHelper->testConnection($serverUrl,$userName,$password,$confirmPassword,$company,$storeId);
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }
    }
}
