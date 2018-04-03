<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\System\Config;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Kensium\Amconnector\Helper\Licensecheck;
class ApplyLicense extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $config;

    protected $inlineTranslation;

    protected $_transportBuilder;

    protected $cacheTypeList;

    protected $licenseHelper;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\Config\Model\ResourceModel\Config $config
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $_transportBuilder
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param Licensecheck $licenseHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Config\Model\ResourceModel\Config $config,
        StateInterface $inlineTranslation,
        TransportBuilder    $_transportBuilder,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        Licensecheck $licenseHelper
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_messageManager = $context->getMessageManager();
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $_transportBuilder;
        $this->config = $config;
        $this->cacheTypeList= $cacheTypeList;
        $this->licenseHelper = $licenseHelper;
        parent::__construct($context);
    }



    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $licenseKey = $this->getRequest()->getParam('license');
        $store_id = $this->getRequest()->getParam('current_store_id');
        $licenseStatus = $this->licenseHelper->checkLicense($licenseKey,0,$store_id);
        $status = explode('|',$licenseStatus);
        if (strtolower($status[0]) == 'valid') {
            $checkComma = substr(trim($status[1]), -1);
            if ($checkComma == ',')
                $storeCode = rtrim($status[1],',');
            $this->_messageManager->addSuccess(
                __('License Key is applied successfully to '.$storeCode.' store view!')
            );
        } elseif (strtolower($status[0]) == 'invalid') {
            $this->_messageManager->addError(
                __('The applied license is not valid for this server.  Please check with support on the information provided for the license key.')
            );
        } elseif (strtolower($status[0]) == 'invalid ip address') {
            $this->_messageManager->addError(
                __('The applied license is not valid for this server.  Please check with support on the information provided for the license key.')
            );
        } elseif (strtolower($status[0]) == 'invalid macid') {
            $this->_messageManager->addError(
                __('The applied license is not valid for this server.  Please check with support on the information provided for the license key.')
            );
        }elseif (strtolower($status[0]) == 'invalid store code') {
+            $this->_messageManager->addError(
+                __('The applied license is not valid for this store view.  Please check with support on the information provided for the license key.')
        );
        }
        $resultApply = '1';
        $this->getResponse()->setBody( $resultApply );
    }
}
