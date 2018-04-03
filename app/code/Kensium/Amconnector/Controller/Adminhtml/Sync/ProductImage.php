<?php
/**
 *
 * Copyright © 2016 Kensiumcommerce. All rights reserved.
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Sync;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class ProductImage extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

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
    protected $dataHelper;

    /**
     * @var \Kensium\Amconnector\Helper\ProductImage
     */
    protected $productImageHelper;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param ScopeConfigInterface $scopeConfig
     * @param \Kensium\Amconnector\Helper\Data $dataHelper
     * @param \Kensium\Amconnector\Helper\ProductImage $productImageHelper
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem\Io\File $file,
        ScopeConfigInterface $scopeConfig,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Helper\ProductImage $productImageHelper,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);

        $this->session = $context->getSession();
        $this->file = $file;
        $this->scopeConfig = $scopeConfig;
        $this->dataHelper = $dataHelper;
        $this->productImageHelper = $productImageHelper;
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

	    $productImageFlag = '';
       $syncDirectory = BP . "/var/amconnector/";
        //chmod($syncDirectory, 0777);
        $lockDirectory = $syncDirectory . "lock/";
        
        $this->file->checkAndCreateFolder($lockDirectory);
        $entity = "productimage";
        $lockFile = $lockDirectory . $entity . ".lock";

        $backGroundSync = $this->scopeConfig->getValue('amconnectorsync/background_sync/background_sync');
        if ($backGroundSync) {
            $isLock = $this->dataHelper->chkforDuplicateJob($entity);
            if ($isLock) {
                $this->messageManager->addError("Product Image sync already running");
            } else {
                try {
                    exec("php " . BP . "/bin/magento kensium:sync productimage " . $syncId . " " . $gridSessionStoreId . " COMPLETE MANUAL NULL NULL > /dev/null & 1 & echo $!", $out);
                    if (isset($out[0])) {
                        $pid = $out[0] - 1;
                        file_put_contents($lockFile, $pid . "\n");
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        } else {
            $this->productImageHelper->syncProductImage('COMPLETE', 'MANUAL', $syncId, NULL, $gridSessionStoreId);
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }

    }

}
