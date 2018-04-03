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

class Category extends \Magento\Backend\App\Action
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
    protected $categoryHelper;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param ScopeConfigInterface $scopeConfig
     * @param \Kensium\Amconnector\Helper\Data $amconnectorHelper
     * @param \Kensium\Amconnector\Helper\Category $categoryHelper
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem\Io\File $file,
        ScopeConfigInterface $scopeConfig,
        \Kensium\Amconnector\Helper\Data $amconnectorHelper,
        \Kensium\Amconnector\Helper\Category $categoryHelper,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->dir = $dir;
        $this->file = $file;
        $this->scopeConfig = $scopeConfig;
        $this->amconnectorHelper = $amconnectorHelper;
        $this->categoryHelper = $categoryHelper;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $syncId = $this->getRequest()->getParam('id', NULL);
        $session = $this->session->getData();
        $gridSessionStoreId = 0;
        if ($session['storeId']) {
            $gridSessionStoreId = $session['storeId'];
        }
        $categoryFlag = '';
        $syncDirectory = BP . "/var/amconnector/";
        //@chmod($syncDirectory, 0777);
        $lockDirectory = $syncDirectory . "lock/";
        $this->file->checkAndCreateFolder($lockDirectory);
        $entity = "category";
        $lockFile = $lockDirectory . $entity . ".lock";
        $backGroundSync = $this->scopeConfig->getValue('amconnectorsync/background_sync/background_sync');

        if ($backGroundSync) {
            $isLock = $this->amconnectorHelper->chkforDuplicateJob($entity);
            if ($isLock) {
                $this->messageManager->addError("Category sync already running");
            } else {
                try {
                    exec("php " . BP . "/bin/magento kensium:sync category " . $syncId . " " . $gridSessionStoreId . " COMPLETE MANUAL NULL NULL> /dev/null & 1 & echo $!", $out);
                    if (isset($out[0])) {
                        $pid = $out[0] - 1;
                        file_put_contents($lockFile, $pid . "\n");
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        } else {
            $this->categoryHelper->getCategorySync('COMPLETE', 'MANUAL', $syncId, NULL, $gridSessionStoreId, NULL, NULL, NULL);
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }
    }
}
