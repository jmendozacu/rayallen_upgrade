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

class AcumaticaBaseData extends \Magento\Backend\App\Action
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
       $syncDirectory = BP . "/var/amconnector/";
        $lockDirectory = $syncDirectory . "lock/";
        $this->file->checkAndCreateFolder($lockDirectory);
        $entity = "acumaticaBaseData";
        $lockFile = $lockDirectory . $entity . ".lock";
        $backGroundSync = $this->scopeConfig->getValue('amconnectorsync/background_sync/background_sync');

        if ($backGroundSync) {
            $isLock = $this->amconnectorHelper->chkforDuplicateJob($entity);
            if ($isLock) {
                $this->messageManager->addError("Acumatica Base Data already running");
            } else {
                try{
                    exec("php ".BP."/bin/magento kensium:sync acumaticaBaseData ".$storeId." NULL NULL NULL NULL NULL> /dev/null & 1 & echo $!", $out);
                    if (isset($out[0])) {
                        $pid = $out[0] - 1;
                        file_put_contents($lockFile, $pid . "\n");
                    }
                }catch (Exception $e){
                    echo $e->getMessage();
                }
            }
        } else {
            $this->testConnectionHelper->acumaticaBaseData($storeId);
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }
    }
}
