<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Webapi\Soap;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Locale\ListsInterface;
use Magento\Config\Model\ResourceModel\Config;
use Kensium\Lib;

class TestConnection extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var ResourceConnection|\Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;

    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $resourceModelSync;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Kensium\Amconnector\Model\Category
     */
    protected $amconnectorCategoryFactory;

    /**
     * @var \Magento\Category\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Category\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Data
     */
    protected $xmlHelper;

    /**
     * @var Data
     */
    protected $timeHelper;

    /**
     * @var Url
     */
    protected $urlHelper;

    /**
     * @var Sync
     */
    protected $syncHelper;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var ListsInterface
     */
    protected $localeLists;
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Config
     */
    protected $config;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Category
     */
    protected $categoryResourceModel;
    /**
     * @var
     */
    public $totalTrialRecord;
    /**
     * @var
     */
    public $licenseType;
    /**
     * @var
     */
    protected $categoryLogHelper;
    /**
     * @var Client
     */
    protected $clientHelper;
    /**
     * @var \Kensium\Amconnector\Model\Category
     */
    protected $categoryModel;
    /**
     * @var
     */
    protected $licenseResourceModel;

    protected $_messageManager;

    const IS_LICENSE_INVALID = "Invalid";
    const IS_LICENSE_VALID = "Valid";
    const IS_TIME_VALID = "Valid";


    public function __construct(
        Context $context,
        DateTime $date,
        Sync $syncHelper,
        Timezone $timezone,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Helper\Time $timeHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Kensium\Amconnector\Model\ResourceModel\Category $categoryResourceModel,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        \Kensium\Amconnector\Helper\Data $dataHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Kensium\Synclog\Helper\Category $categoryLogHelper,
        \Kensium\Amconnector\Model\Category $categoryModel,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Model\CategoryFactory $amconnectorCategoryFactory,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\CategoryFactory $category,
        ListsInterface $localeLists,
        Config $config,
        Lib\Common $common
    )
    {
        parent::__construct($context);
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->date = $date;
        $this->syncHelper = $syncHelper;
        $this->urlHelper = $urlHelper;
        $this->xmlHelper = $xmlHelper;
        $this->timeHelper = $timeHelper;
        $this->resourceConnection = $resourceConnection;
        $this->dir = $dir;
        $this->resourceModelSync = $resourceModelSync;
        $this->logger = $context->getLogger();
        $this->messageManager = $messageManager;
        $this->localeLists = $localeLists;
        $this->config = $config;
        $this->amconnectorCategoryFactory = $amconnectorCategoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $category;
        $this->clientHelper = $clientHelper;
        $this->categoryModel = $categoryModel;
        $this->categoryLogHelper = $categoryLogHelper;
        $this->dataHelper = $dataHelper;
        $this->syncResourceModel = $syncResourceModel;
        $this->categoryResourceModel = $categoryResourceModel;
        $this->timezone = $timezone;
        $this->_messageManager = $messageManager;
        $this->licenseResourceModel = $licenseResourceModel;
        $this->common = $common;
    }

    public function testConnection($serverUrl, $userName, $password, $confirmPassword, $companyName,$scopeId)
    {

        $logFileName = 'testConnection.log';
        $logPath = $this->dataHelper->getLogPath();

        $configParameters = array();
        $logViewFileName = BP.$logPath."testConnection/".$this->date->date('Y-m-d') ."/" . $logFileName;
        if ($scopeId == 0) {
            $scopeType = 'default';
        } else {
            $scopeType = 'stores';
        }
        if (substr($serverUrl, -1) == '/') {
            $serverUrl = $serverUrl;
        } else {
            $serverUrl = $serverUrl . '/';
        }
        if ($password == '' || $confirmPassword == '') {
            if ($password == '') {
                $password = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/password', $scopeType, $scopeId);
                if ($password == '' && $scopeId == 1) {
                    $password = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/password');
                }
            }
            if ($confirmPassword == '') {
                $confirmPassword = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/confirmPassword', $scopeType, $scopeId);
                if ($confirmPassword == '' && $scopeId == 1) {
                    $confirmPassword = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/confirmPassword');
                }
            }
        }

        $errorFlag = false;
        if (trim($serverUrl) == '' || trim($userName) == '' || trim($password) == '' || trim($confirmPassword) == '' ||  trim($password) != trim($confirmPassword)) {
            $errorFlag = true;
        }
        $txt = "Info : Test connection process initiated!";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
        if (!$errorFlag) {
            $url = $this->common->getBasicConfigUrl($serverUrl);
            $request = array('name' => $userName, 'password' => $password, 'company' => $companyName, 'locale' => 'en-gb');
            $acumaticaConnection = $this->common->login($request, $configParameters,$url);
            if($acumaticaConnection != 0){
                $this->config->saveConfig('amconnectorcommon/amconnectoracucon/serverUrl', $serverUrl, $scopeType, $scopeId);
                $this->config->saveConfig('amconnectorcommon/amconnectoracucon/userName', $userName, $scopeType, $scopeId);
                $this->config->saveConfig('amconnectorcommon/amconnectoracucon/password', $password, $scopeType, $scopeId);
                $this->config->saveConfig('amconnectorcommon/amconnectoracucon/confirmPassword', $confirmPassword, $scopeType, $scopeId);
                $this->config->saveConfig('amconnectorcommon/amconnectoracucon/companyName', $companyName, $scopeType, $scopeId);
                if($scopeId == 1){
                    $this->config->saveConfig('amconnectorcommon/amconnectoracucon/serverUrl', $serverUrl, 'default', 0);
                    $this->config->saveConfig('amconnectorcommon/amconnectoracucon/userName', $userName, 'default', 0);
                    $this->config->saveConfig('amconnectorcommon/amconnectoracucon/password', $password, 'default', 0);
                    $this->config->saveConfig('amconnectorcommon/amconnectoracucon/confirmPassword', $confirmPassword, 'default', 0);
                    $this->config->saveConfig('amconnectorcommon/amconnectoracucon/companyName', $companyName, 'default', 0);
                }

                $amconnectorConfigUrl = $this->common->getBasicConfigUrl($serverUrl);
                /**
                 * Default Warehouse Data
                 */
                $txt = "Info : Fetching default warehouse data...";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                $csvWarehouseData = $this->common->getEnvelopeData('WAREHOUSELIST');
                $warehouseXMLGetRequest = $csvWarehouseData['envelope'];
                $warehouseAction = $csvWarehouseData['envName']."/".$csvWarehouseData['envVersion']."/".$csvWarehouseData['methodName'];
                $getWarehouseResponse = $this->common->getAcumaticaResponse($request,$warehouseXMLGetRequest,$amconnectorConfigUrl,$warehouseAction,$scopeId);
                if(isset($getWarehouseResponse->Body->GetListResponse->GetListResult))
                    $warehouseXmlData = $getWarehouseResponse->Body->GetListResponse->GetListResult;
                else
                    $warehouseXmlData = array();

                $warehouseTotalData = $this->xmlHelper->xml2array($warehouseXmlData);

                if(count($warehouseTotalData) > 0 && isset($warehouseXmlData) && !empty($warehouseXmlData))
                {
                    $this->syncResourceModel->deleteWarehouseData($scopeId);
                    if(isset($warehouseTotalData['Entity']['WarehouseID']['Value'])){
                        if($warehouseTotalData['Entity']['Active']['Value'] == 'True') {
                            $warehouseName = trim($warehouseTotalData['Entity']['WarehouseID']['Value']);
                            $this->syncResourceModel->insertWarehouseData($warehouseName, $scopeId);
                        }
                    }else{
                        foreach($warehouseTotalData ['Entity'] as $warehouseData)
                        {
                            $warehouseData = $this->xmlHelper->xml2array($warehouseData);

                            if(strtolower($warehouseData['Active']['Value']) == 'true')
                            {
                                $warehouseName = trim($warehouseData['WarehouseID']['Value']);
                                $this->syncResourceModel->insertWarehouseData($warehouseName, $scopeId);
                            }
                        }
                    }
                    $txt = "Info : Default warehouse data updated successfully!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                }else{
                    $txt = "Error: Failed to update default warehouse data!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                }

                /**
                 * Customer Class Details
                 */
                $txt = "Info : Fetching default customer class data...";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                $csvCustomerClassData = $this->common->getEnvelopeData('DEFAULTCUSTOMERCLASS');
                $customerClassXMLGetRequest = $csvCustomerClassData['envelope'];
                $customerClassAction = $csvCustomerClassData['envName']."/".$csvCustomerClassData['envVersion']."/".$csvCustomerClassData['methodName'];
                $getCustomerClassResponse = $this->common->getAcumaticaResponse($request,$customerClassXMLGetRequest,$amconnectorConfigUrl, $customerClassAction);
                if(isset($getCustomerClassResponse->Body->GetListResponse->GetListResult))
                    $customerClassXmlData = $getCustomerClassResponse->Body->GetListResponse->GetListResult;
                else
                    $customerClassXmlData = array();

                $customerClassTotalData = $this->xmlHelper->xml2array($customerClassXmlData);
                if (count($customerClassTotalData) > 0 && isset($customerClassTotalData)  && !empty($customerClassTotalData)) {
                    $this->syncResourceModel->deleteCustomerClassData($scopeId);
                    if (isset($customerClassTotalData['Entity']['ClassID']['Value'])) {
                        $customerClassName = trim($customerClassTotalData['Entity']['ClassID']['Value']);
                        $this->syncResourceModel->insertCustomerClassData($customerClassName, $scopeId);
                    } else {
                        foreach ($customerClassTotalData ['Entity'] as $customerClassData) {
                            $customerClassName = trim($customerClassData->ClassID->Value);
                            $this->syncResourceModel->insertCustomerClassData($customerClassName, $scopeId);
                        }
                    }
                    $txt = "Info : Default customer class data updated successfully!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                } else {
                    $txt = "Error: Failed to update default customer class data!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                }

                /**
                 * Customer Terms Details
                 */
                $txt = "Info : Fetching default customer terms data...";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                $csvCustomerTermData = $this->common->getEnvelopeData('DEFAULTCUSTOMERTERMS');
                $customerTermXMLGetRequest = $csvCustomerTermData['envelope'];
                $customerTermAction = $csvCustomerTermData['envName']."/".$csvCustomerTermData['envVersion']."/".$csvCustomerTermData['methodName'];
                $getCustomerTermResponse = $this->common->getAcumaticaResponse($request,$customerTermXMLGetRequest,$amconnectorConfigUrl, $customerTermAction);
                if(isset($getCustomerTermResponse->Body->GetListResponse->GetListResult))
                    $customerTermXmlData = $getCustomerTermResponse->Body->GetListResponse->GetListResult;
                else
                    $customerTermXmlData = array();

                $customerTermTotalData = $this->xmlHelper->xml2array($customerTermXmlData);
                if (count($customerTermTotalData) > 0 && isset($customerTermTotalData)  && !empty($customerTermTotalData)) {
                    $this->syncResourceModel->deleteCustomerTermData($scopeId);
                    if (isset($customerTermTotalData['Entity']['TermsID']['Value'])) {
                        $customerTermName = trim($customerTermTotalData['Entity']['TermsID']['Value']);
                        $this->syncResourceModel->insertCustomerTermData($customerTermName, $scopeId);
                    } else {
                        foreach ($customerTermTotalData ['Entity'] as $customerTermData) {
                            $customerTermName = trim($customerTermData->TermsID->Value);
                            $this->syncResourceModel->insertCustomerTermData($customerTermName, $scopeId);
                        }
                    }
                    $txt = "Info : Default customer terms data updated successfully!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                } else {
                    $txt = "Error: Failed to update default customer terms data!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                }

                /**
                 * Customer cycle
                 */
                $txt = "Info : Fetching default customer cycle...";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                $csvCustomerCycleData = $this->common->getEnvelopeData('DEFAULTCUSTOMERSTATEMENTCYCLE');
                $customerCycleXMLGetRequest = $csvCustomerCycleData['envelope'];
                $customerCycleAction = $csvCustomerCycleData['envName']."/".$csvCustomerCycleData['envVersion']."/".$csvCustomerCycleData['methodName'];
                $getCustomerCycleResponse = $this->common->getAcumaticaResponse($request,$customerCycleXMLGetRequest,$amconnectorConfigUrl, $customerCycleAction);
                if(isset($getCustomerCycleResponse->Body->GetListResponse->GetListResult))
                    $customerCycleXmlData = $getCustomerCycleResponse->Body->GetListResponse->GetListResult;
                else
                    $customerCycleXmlData = array();

                $customerCycleTotalData = $this->xmlHelper->xml2array($customerCycleXmlData);
                if (count($customerCycleTotalData) > 0 && isset($customerCycleTotalData)  && !empty($customerCycleTotalData)) {
                    $this->syncResourceModel->deleteCustomerCycleData($scopeId);
                    if (isset($customerCycleTotalData['Entity']['CycleID']['Value'])) {
                        $customerCycleName = trim($customerCycleTotalData['Entity']['CycleID']['Value']);
                        $this->syncResourceModel->insertCustomerCycleData($customerCycleName, $scopeId);
                    } else {
                        foreach ($customerCycleTotalData ['Entity'] as $customerCycleData) {
                            $customerCycleName = trim($customerCycleData->CycleID->Value);
                            $this->syncResourceModel->insertCustomerCycleData($customerCycleName, $scopeId);
                        }
                    }
                    $txt = "Info : Default customer cycle data updated successfully!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                } else {
                    $txt = "Error: Failed to update default customer cycle data!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                }

                /**
                 * Sales Account
                 */
                $txt = "Info : Fetching default sales account data...";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                $csvSalesAccountData = $this->common->getEnvelopeData('DEFAULTSALESACCOUNT');
                $salesAccountXMLGetRequest = $csvSalesAccountData['envelope'];
                $salesAccountAction = $csvSalesAccountData['envName']."/".$csvSalesAccountData['envVersion']."/".$csvSalesAccountData['methodName'];
                $getSalesAccountResponse = $this->common->getAcumaticaResponse($request,$salesAccountXMLGetRequest,$amconnectorConfigUrl, $salesAccountAction);
                if(isset($getSalesAccountResponse->Body->GetListResponse->GetListResult))
                    $salesAccountXmlData = $getSalesAccountResponse->Body->GetListResponse->GetListResult;
                else
                    $salesAccountXmlData = array();

                $salesAccountTotalData = $this->xmlHelper->xml2array($salesAccountXmlData);
                if (count($salesAccountTotalData) > 0 && isset($salesAccountTotalData)  && !empty($salesAccountTotalData)) {
                    $this->syncResourceModel->deleteSalesAccountData($scopeId);
                    if (isset($salesAccountTotalData['Entity']['Account']['Value'])) {
                        if($salesAccountTotalData['Entity']['Active']['Value']){
                            $customerCycleName = trim($salesAccountTotalData['Entity']['Account']['Value']);
                            $this->syncResourceModel->insertSalesAccountData($customerCycleName, $scopeId);
                        }
                    } else {
                        foreach ($salesAccountTotalData['Entity'] as $salesAccountData) {
                            if($salesAccountData->Active->Value == 'true') {
                                $salesAccount = trim($salesAccountData->Account->Value);
                                $this->syncResourceModel->insertSalesAccountData($salesAccount, $scopeId);
                            }
                        }
                    }
                    $txt = "Info : Default sales account data updated successfully!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                } else {
                    $txt = "Error: Failed to update default sales account!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                }

                /**
                 * Payment Method Details
                 */
                $txt = "Info : Fetching default payment method data...";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

                $csvPaymentMethodData = $this->common->getEnvelopeData('DEFAULTPAYMENTMETHOD');
                $paymentMethodXMLGetRequest = $csvPaymentMethodData['envelope'];
                $paymentMethodAction = $csvPaymentMethodData['envName']."/".$csvPaymentMethodData['envVersion']."/".$csvPaymentMethodData['methodName'];
                $getPaymentMethodResponse = $this->common->getAcumaticaResponse($request,$paymentMethodXMLGetRequest,$amconnectorConfigUrl, $paymentMethodAction);
                if(isset($getPaymentMethodResponse->Body->GetListResponse->GetListResult))
                    $paymentMethodXmlData = $getPaymentMethodResponse->Body->GetListResponse->GetListResult;
                else
                    $paymentMethodXmlData = array();

                $paymentMethodTotalData = $this->xmlHelper->xml2array($paymentMethodXmlData);
                if(count($paymentMethodTotalData) > 0 && isset($paymentMethodTotalData)  && !empty($paymentMethodTotalData)){
                    $paymentStatus = $this->syncResourceModel->insertPaymentMethodData($paymentMethodXmlData,$scopeId);
                    if($paymentStatus == 0){
                        $txt = "Info : Default payment method data updated successfully!";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    }else{
                        $txt = "Error: Failed to update default payment method data!";
                        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                    }
                }else{
                    $txt = "Error: Failed to update default payment method data!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                }
                $txt = "Info : Acumatica connection is successful!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

            }else{
                $txt = "Error: Test connection failed. Please try again";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);

            }
        }else{
            $txt = "Error: Test connection failed. Please try again";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

        }
        //$this->clientHelper->clearCache();
        $txt = "Info : Test Connection completed!";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
    }

    /**
     * Reload Acumatica Default Data
     */
    public function acumaticaBaseData($scopeId)
    {
        $logFileName = 'acumaticaBaseData.log';
        $logPath = $this->dataHelper->getLogPath();
        $logViewFileName = BP.$logPath."acumaticaBaseData/".$this->date->date('Y-m-d') ."/" . $logFileName;
        $configParameters =  array();
        $configParameters = $this->dataHelper->getConfigParameters($scopeId);

        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl', 'stores', $scopeId);
        if($serverUrl == ''){
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
        }

        $txt = "Info : Default configuration process initiated!";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);

        if($serverUrl != ''){
            /**
             * Default Warehouse Data
             */
            $txt = "Info : Fetching default warehouse data...";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $url = $this->urlHelper->getBasicConfigUrl($serverUrl);
            $csvWarehouseData = $this->common->getEnvelopeData('WAREHOUSELIST');
            $warehouseXMLGetRequest = $csvWarehouseData['envelope'];
            $warehouseAction = $csvWarehouseData['envName']."/".$csvWarehouseData['envVersion']."/".$csvWarehouseData['methodName'];
            $getWarehouseResponse = $this->common->getAcumaticaResponse($configParameters,$warehouseXMLGetRequest,$url, $warehouseAction);
            $warehouseXmlData = $getWarehouseResponse->Body->GetListResponse->GetListResult;
            $warehouseTotalData = $this->xmlHelper->xml2array($warehouseXmlData);
            if(count($warehouseTotalData) > 0){
                $this->syncResourceModel->deleteWarehouseData($scopeId);
                if(isset($warehouseTotalData['Entity']['WarehouseID']['Value'])){
                    if(strtolower($warehouseTotalData['Entity']['Active']['Value']) == 'true') {
                        $warehouseName = trim($warehouseTotalData['Entity']['WarehouseID']['Value']);
                        $this->syncResourceModel->insertWarehouseData($warehouseName, $scopeId);
                    }
                }else{
                    foreach($warehouseTotalData ['Entity'] as $warehouseData){
                        /* If warehouse is active in acumatica */
                        if(strtolower($warehouseData->Active->Value) == 'true') {
                            $warehouseName = trim($warehouseData->WarehouseID->Value);
                            $this->syncResourceModel->insertWarehouseData($warehouseName, $scopeId);
                        }
                    }
                }
                $txt = "Info : Default warehouse data updated successfully!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            }else{
                $txt = "Error: Failed to update default warehouse data!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            }
            /**
             * Default Customer Class
             */
            $txt = "Info : Fetching default customer class data...";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            $amconnectorConfigUrl = $this->urlHelper->getBasicConfigUrl($serverUrl);
            $csvCustomerClassData = $this->common->getEnvelopeData('DEFAULTCUSTOMERCLASS');
            $customerClassXMLGetRequest = $csvCustomerClassData['envelope'];
            $customerClassAction = $csvCustomerClassData['envName']."/".$csvCustomerClassData['envVersion']."/".$csvCustomerClassData['methodName'];
            $getCustomerClassResponse = $this->common->getAcumaticaResponse($configParameters,$customerClassXMLGetRequest,$amconnectorConfigUrl, $customerClassAction);
            $customerClassXmlData = $getCustomerClassResponse->Body->GetListResponse->GetListResult;
            $customerClassTotalData = $this->xmlHelper->xml2array($customerClassXmlData);
            if (count($customerClassTotalData) > 0) {
                $this->syncResourceModel->deleteCustomerClassData($scopeId);
                if (isset($customerClassTotalData['Entity']['ClassID']['Value'])) {
                    $customerClassName = trim($customerClassTotalData['Entity']['ClassID']['Value']);
                    $this->syncResourceModel->insertCustomerClassData($customerClassName, $scopeId);
                } else {
                    foreach ($customerClassTotalData ['Entity'] as $customerClassData) {
                        $customerClassName = trim($customerClassData->ClassID->Value);
                        $this->syncResourceModel->insertCustomerClassData($customerClassName, $scopeId);
                    }
                }
                $txt = "Info : Default customer class data updated successfully!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            } else {
                $txt = "Error: Failed to update default customer class data!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            }

            /**
             * Customer Terms Details
             */
            $txt = "Info : Fetching default customer terms data...";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

            $csvCustomerTermData = $this->common->getEnvelopeData('DEFAULTCUSTOMERTERMS');
            $customerTermXMLGetRequest = $csvCustomerTermData['envelope'];
            $customerTermAction = $csvCustomerTermData['envName']."/".$csvCustomerTermData['envVersion']."/".$csvCustomerTermData['methodName'];
            $getCustomerTermResponse = $this->common->getAcumaticaResponse($configParameters,$customerTermXMLGetRequest,$amconnectorConfigUrl, $customerTermAction);
            $customerTermXmlData = $getCustomerTermResponse->Body->GetListResponse->GetListResult;
            $customerTermTotalData = $this->xmlHelper->xml2array($customerTermXmlData);
            if (count($customerTermTotalData) > 0) {
                $this->syncResourceModel->deleteCustomerTermData($scopeId);
                if (isset($customerTermTotalData['Entity']['TermsID']['Value'])) {
                    $customerTermName = trim($customerTermTotalData['Entity']['TermsID']['Value']);
                    $this->syncResourceModel->insertCustomerTermData($customerTermName, $scopeId);
                } else {
                    foreach ($customerTermTotalData ['Entity'] as $customerTermData) {
                        $customerTermName = trim($customerTermData->TermsID->Value);
                        $this->syncResourceModel->insertCustomerTermData($customerTermName, $scopeId);
                    }
                }
                $txt = "Info : Default customer terms data updated successfully!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            } else {
                $txt = "Error: Failed to update default customer terms data!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            }

            /**
             * Customer cycle
             */
            $txt = "Info : Fetching default customer cycle...";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

            $csvCustomerCycleData = $this->common->getEnvelopeData('DEFAULTCUSTOMERSTATEMENTCYCLE');
            $customerCycleXMLGetRequest = $csvCustomerCycleData['envelope'];
            $customerCycleAction = $csvCustomerCycleData['envName']."/".$csvCustomerCycleData['envVersion']."/".$csvCustomerCycleData['methodName'];
            $getCustomerCycleResponse = $this->common->getAcumaticaResponse($configParameters,$customerCycleXMLGetRequest,$amconnectorConfigUrl, $customerCycleAction);
            $customerCycleXmlData = $getCustomerCycleResponse->Body->GetListResponse->GetListResult;
            $customerCycleTotalData = $this->xmlHelper->xml2array($customerCycleXmlData);
            if (count($customerCycleTotalData) > 0) {
                $this->syncResourceModel->deleteCustomerCycleData($scopeId);
                if (isset($customerCycleTotalData['Entity']['CycleID']['Value'])) {
                    $customerCycleName = trim($customerCycleTotalData['Entity']['CycleID']['Value']);
                    $this->syncResourceModel->insertCustomerCycleData($customerCycleName, $scopeId);
                } else {
                    foreach ($customerCycleTotalData ['Entity'] as $customerCycleData) {
                        $customerCycleName = trim($customerCycleData->CycleID->Value);
                        $this->syncResourceModel->insertCustomerCycleData($customerCycleName, $scopeId);
                    }
                }
                $txt = "Info : Default customer cycle data updated successfully!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            } else {
                $txt = "Error: Failed to update default customer cycle data!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            }

            /**
             * Sales Account
             */
            $txt = "Info : Fetching default sales account data...";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

            $csvSalesAccountData = $this->common->getEnvelopeData('DEFAULTSALESACCOUNT');
            $salesAccountXMLGetRequest = $csvSalesAccountData['envelope'];
            $salesAccountAction = $csvSalesAccountData['envName']."/".$csvSalesAccountData['envVersion']."/".$csvSalesAccountData['methodName'];
            $getSalesAccountResponse = $this->common->getAcumaticaResponse($configParameters,$salesAccountXMLGetRequest,$amconnectorConfigUrl, $salesAccountAction);
            $salesAccountXmlData = $getSalesAccountResponse->Body->GetListResponse->GetListResult;
            $salesAccountTotalData = $this->xmlHelper->xml2array($salesAccountXmlData);
            if (count($salesAccountTotalData) > 0) {
                $this->syncResourceModel->deleteSalesAccountData($scopeId);
                if (isset($salesAccountTotalData['Entity']['Account']['Value'])) {
                    if($salesAccountTotalData['Entity']['Active']['Value']){
                        $customerCycleName = trim($salesAccountTotalData['Entity']['Account']['Value']);
                        $this->syncResourceModel->insertSalesAccountData($customerCycleName, $scopeId);
                    }
                } else {
                    foreach ($salesAccountTotalData['Entity'] as $salesAccountData) {
                        if($salesAccountData->Active->Value == 'true') {
                            $salesAccount = trim($salesAccountData->Account->Value);
                            $this->syncResourceModel->insertSalesAccountData($salesAccount, $scopeId);
                        }
                    }
                }
                $txt = "Info : Default sales account data updated successfully!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            } else {
                $txt = "Error: Failed to update default sales account!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            }
            /**
             * Payment Method Details
             */
            $txt = "Info : Fetching default payment method data...";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);

            $csvPaymentMethodData = $this->common->getEnvelopeData('DEFAULTPAYMENTMETHOD');
            $paymentMethodXMLGetRequest = $csvPaymentMethodData['envelope'];
            $paymentMethodAction = $csvPaymentMethodData['envName']."/".$csvPaymentMethodData['envVersion']."/".$csvPaymentMethodData['methodName'];
            $getPaymentMethodResponse = $this->common->getAcumaticaResponse($configParameters,$paymentMethodXMLGetRequest,$amconnectorConfigUrl, $paymentMethodAction);
            $paymentMethodXmlData = $getPaymentMethodResponse->Body->GetListResponse->GetListResult;
            $paymentMethodTotalData = $this->xmlHelper->xml2array($paymentMethodXmlData);
            if(count($paymentMethodTotalData) > 0){
                $paymentStatus = $this->syncResourceModel->insertPaymentMethodData($paymentMethodXmlData,$scopeId);
                if($paymentStatus == 0){
                    $txt = "Info : Default payment method data updated successfully!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                }else{
                    $txt = "Error: Failed to update default payment method data!";
                    $this->dataHelper->writeLogToFile($logViewFileName, $txt);
                }
            }else{
                $txt = "Error: Failed to update default payment method data!";
                $this->dataHelper->writeLogToFile($logViewFileName, $txt);
            }
        }else{
            $txt = "Error: Default configuration update failed. Please try again";
            $this->dataHelper->writeLogToFile($logViewFileName, $txt);
        }
        $txt = "Info : Default configuration completed!";
        $this->dataHelper->writeLogToFile($logViewFileName, $txt);
    }
}
