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
use Magento\Backend\Model\Session;
class ExportLicenseRequest extends Action
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

    protected $backendSession;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\Config\Model\ResourceModel\Config $config
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $_transportBuilder
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Config\Model\ResourceModel\Config $config,
        StateInterface $inlineTranslation,
        TransportBuilder    $_transportBuilder,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_messageManager = $context->getMessageManager();
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $_transportBuilder;
        $this->config = $config;
        $this->cacheTypeList= $cacheTypeList;
        $this->backendSession = $context->getSession();
        parent::__construct($context);
    }



    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $domainURLs = $this->getRequest()->getParam('domain');
        $macIdRequest = $this->getRequest()->getParam('macid');
        $ipAddressRequest = $this->getRequest()->getParam('ipaddress');
        $emailRequest = $this->getRequest()->getParam('email');
        $comments = $this->getRequest()->getParam('comment');
        $licenseTypeRequest = $this->getRequest()->getParam('license_type');
        if (!($domainURLs == '' || $ipAddressRequest == '' || $macIdRequest == '' || $emailRequest == '' || $licenseTypeRequest == '')) {
            //save all the fields data, iff no error
            $scopeType= "default";
            $scopeId = 0;

            $this->config->saveConfig('license/license_request/domainnames', $domainURLs, $scopeType, $scopeId);
            $this->config->saveConfig('license/license_request/ipaddress', $ipAddressRequest, $scopeType, $scopeId);
            $this->config->saveConfig('license/license_request/macids', $macIdRequest, $scopeType, $scopeId);
            $this->config->saveConfig('license/license_request/emailrecipient', $emailRequest, $scopeType, $scopeId);
            $this->config->saveConfig('license/license_request/licensetype', $licenseTypeRequest, $scopeType, $scopeId);
            $this->config->saveConfig('license/license_request/comments', $comments, $scopeType, $scopeId);
            $type = 'config';
            $this->cacheTypeList->cleanType($type);
            $this->backendSession->setIsAllValuesSelected(1);
            $this->getResponse()->setBody( 1 );
        }
    }
}
