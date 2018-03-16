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
use Kensium\Amconnector\Model\LicensecheckFactory;
use Kensium\Amconnector\Helper\Sync;

class SendLicenseRequest extends Action {

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
    protected $licenseFactory;
    protected $syncHelper;
    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\Config\Model\ResourceModel\Config $config
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $_transportBuilder
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param LicensecheckFactory $licenseFactory
     * @param Sync $syncHelper
     */
    public function __construct(
    Context $context, JsonFactory $resultJsonFactory, \Magento\Config\Model\ResourceModel\Config $config, StateInterface $inlineTranslation, TransportBuilder $_transportBuilder, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, LicensecheckFactory $licenseFactory, Sync $syncHelper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_messageManager = $context->getMessageManager();
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $_transportBuilder;
        $this->config = $config;
        $this->cacheTypeList = $cacheTypeList;
        $this->licenseFactory = $licenseFactory;
        $this->syncHelper = $syncHelper;
        parent::__construct($context);
    }

    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute() {
        $domainURLs = $this->getRequest()->getParam('domain');
        $comments = $this->getRequest()->getParam('comment');
        $macIdRequest = $this->getRequest()->getParam('macid');
        $ipAddressRequest = $this->getRequest()->getParam('ipaddress');
        $emailRequest = $this->getRequest()->getParam('email');
        $licenseTypeRequest = $this->getRequest()->getParam('license_type');
        $isNewLicenseRequest = false;
        if ($domainURLs == '' || $ipAddressRequest == '' || $emailRequest == '' || $licenseTypeRequest == '') {
            $this->_messageManager->addError(
                    __('Please Select all required Fields')
            );
            $resultLicense = 0;
        } else {
            $licenseCollection = $this->licenseFactory->create()->getCollection();
            $keys = '';
            foreach ($licenseCollection as $license) {
                $keys .= base64_decode($license->getData('license_key')) . ',';
            }

            if ($keys != '') {
                $keys = chop($keys, ",");
                $isNewLicenseRequest = true;
            }

            $emailTemplateVariables = array();
            $emailTemplateVariables['domain'] = $domainURLs;
            $emailTemplateVariables['ip_address'] = $ipAddressRequest;
            if($macIdRequest){
                $emailTemplateVariables['mac_id'] = $macIdRequest;
            }
            $emailTemplateVariables['license_type'] = $licenseTypeRequest;
            $emailTemplateVariables['comments'] = $comments;
            $from = array('email' => $emailRequest, 'name' => $emailRequest);
            try {
                $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_ADMINHTML, 'store' => 1);
                $this->inlineTranslation->suspend();
                $to = array('satheeshb@kensium.com', 'Acumatica - License Request');
                $transport = $this->_transportBuilder->setTemplateIdentifier('license_request')
                        ->setTemplateOptions($templateOptions)
                        ->setTemplateVars($emailTemplateVariables)
                        ->setFrom($from)
                        ->addTo($to)
                        ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch (\Exception $ex) {
                //echo $ex->getMessage();exit;
            }
            $resultLicense = 1;
            $scopeType = "default";
            $scopeId = 0;
            if ($isNewLicenseRequest) {
                $this->config->saveConfig('license/add_domain_request/add_domainnames', $domainURLs, $scopeType, $scopeId);
                $this->config->saveConfig('license/add_domain_request/add_macids', $macIdRequest, $scopeType, $scopeId);
                $this->config->saveConfig('license/add_domain_request/add_ipaddress', $ipAddressRequest, $scopeType, $scopeId);
                $this->config->saveConfig('license/add_domain_request/add_email_recipient', $emailRequest, $scopeType, $scopeId);
                $this->config->saveConfig('license/add_domain_request/add_license_type', $licenseTypeRequest, $scopeType, $scopeId);
                $this->config->saveConfig('license/add_domain_request/addcomments', $comments, $scopeType, $scopeId);
            } else {
                //save all the fields data, iff no error
                $this->config->saveConfig('license/license_request/domainnames', $domainURLs, $scopeType, $scopeId);
                $this->config->saveConfig('license/license_request/ipaddress', $ipAddressRequest, $scopeType, $scopeId);
                $this->config->saveConfig('license/license_request/macids', $macIdRequest, $scopeType, $scopeId);
                $this->config->saveConfig('license/license_request/emailrecipient', $emailRequest, $scopeType, $scopeId);
                $this->config->saveConfig('license/license_request/licensetype', $licenseTypeRequest, $scopeType, $scopeId);
                $this->config->saveConfig('license/license_request/comments', $comments, $scopeType, $scopeId);
            }
            $type = 'config';
            $this->cacheTypeList->cleanType($type);
            $this->_messageManager->addSuccess(
                    __('License Key request sent successfully!')
            );
        }
        $this->getResponse()->setBody($resultLicense);
    }
}
