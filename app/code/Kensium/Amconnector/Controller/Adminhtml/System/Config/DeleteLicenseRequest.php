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
use Kensium\Amconnector\Model\LicensecheckFactory;
use Magento\Store\Model\StoreManagerInterface;
class DeleteLicenseRequest extends Action
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

    protected $licenseFactory;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\Config\Model\ResourceModel\Config $config
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $_transportBuilder
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param Licensecheck $licenseHelper
     * @param LicensecheckFactory $licenseFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Config\Model\ResourceModel\Config $config,
        StateInterface $inlineTranslation,
        TransportBuilder    $_transportBuilder,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        Licensecheck $licenseHelper,
        LicensecheckFactory $licenseFactory,
        StoreManagerInterface $storeManagerInterface
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_messageManager = $context->getMessageManager();
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $_transportBuilder;
        $this->config = $config;
        $this->cacheTypeList= $cacheTypeList;
        $this->licenseHelper = $licenseHelper;
        $this->licenseFactory = $licenseFactory;
        $this->storeManagerInterface = $storeManagerInterface;
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
        if ($domainURLs == '') {
            $this->_messageManager->addError("Please Select all required Fields");
            $resultApply = '0';
        }
        $comments = $this->getRequest()->getParam('comments');
        $domainURLsArray = explode(',', $domainURLs);
        array_pop($domainURLsArray);
        $licenseKeys = '';
        foreach($domainURLsArray as $domainURL){
            $checkSlash = substr(trim($domainURL), -1);
                if ($checkSlash != '/')
                    $domain = explode('/', $domainURL);
                else
                    $domain = explode('/', $domainURL);
                    array_pop($domain);
            
            $storeCode = trim(array_pop($domain));
            $store_id = $this->storeManagerInterface->getStore($storeCode)->getId();
            $licenseCollection = $this->licenseFactory->create()->getCollection()->addFieldToFilter('store_id', $store_id);
            $licenseData = $licenseCollection->getData();
            if(count($licenseData) > 0){
                if(strpos($domainURL,$licenseData[0]['license_url']) !== false){
                    $licenseKeys .= base64_decode($licenseData[0]['license_key']) . ',';
                }
            }
        }
        $emailRequest = $this->licenseHelper->getAdminEmail();
        $emailTemplateVariables = array();
        $emailTemplateVariables['licensekeys'] = $licenseKeys;
        $emailTemplateVariables['domain'] = $domainURLs;
        $emailTemplateVariables['comments'] = $comments;
        $from = array('email' => $emailRequest, 'name' => $emailRequest);

        if($licenseKeys != ''){
        try {
            $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_ADMINHTML, 'store' =>1);
            $this->inlineTranslation->suspend();
            $to = array('satheeshb@kensium.com', 'Acumatica - Delete License Request');
            $transport = $this->_transportBuilder->setTemplateIdentifier('license_delete_request')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($emailTemplateVariables)
                ->setFrom($from)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        }catch (\Exception $ex){
            echo $ex->getMessage();
        }
           $this->_messageManager->addSuccess("License Delete request sent successfully!"); 
           $resultApply = '1';
        }else{
            $this->_messageManager->addError("No License To Delete For Requested Domain");
            $resultApply = '0';
        }
        $this->config->saveConfig('license/delete_domain/domain_urls', $domainURLs, 'default', 0);
        $this->config->saveConfig('license/delete_domain/comments', $comments, 'default', 0);
        $type = 'config';
        $this->cacheTypeList->cleanType($type);
        $this->getResponse()->setBody( $resultApply );
    }
}
