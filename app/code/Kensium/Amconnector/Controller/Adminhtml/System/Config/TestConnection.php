<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\System\Config;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;

class TestConnection extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Kensium\Amconnector\Helper\Sync
     */
    protected $syncHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $config;

    /**
     * @var \Kensium\Amconnector\Model\Connection
     */
    protected $amconnectorConnection;

    /**
     * @var
     */
    protected $clientHelper;

    /**
     * @var
     */
    protected $xmlHelper;
	/**
     * @var
     */    
	protected $urlHelper;
	/**
     * @var
     */
    protected $baseDirPath;
	/**
     * @var
     */
    protected $syncResourceModel;
    /**
     * @var
     */
    protected $_messageManager;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Kensium\Amconnector\Helper\Sync $syncHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Config\Model\ResourceModel\Config $config
     * @param \Kensium\Amconnector\Model\Connection $amconnectorConnection
     * @param \Kensium\Amconnector\Helper\Client $clientHelper
     * @param \Kensium\Amconnector\Helper\Xml $xmlHelper
     * @param \Kensium\Amconnector\Helper\Url $urlHelper
     * @param \Magento\Framework\App\Filesystem\DirectoryList $baseDirPath
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Kensium\Amconnector\Helper\Sync $syncHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Config\Model\ResourceModel\Config $config,
        \Kensium\Amconnector\Model\Connection $amconnectorConnection,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Magento\Framework\App\Filesystem\DirectoryList $baseDirPath,
        \Kensium\Amconnector\Model\ResourceModel\Sync   $syncResourceModel
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->syncHelper = $syncHelper;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->config = $config;
        $this->amconnectorConnection = $amconnectorConnection;
        $this->clientHelper = $clientHelper;
        $this->xmlHelper = $xmlHelper;
        $this->urlHelper = $urlHelper;
        $this->baseDirPath = $baseDirPath;
        $this->syncResourceModel = $syncResourceModel;
        $this->_messageManager = $context->getMessageManager();
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    protected function _check()
    {
        $baseRootPath = $this->baseDirPath->getRoot();
        $scope = $this->getRequest()->getParam('scope');
        if ($scope == "default") {
            $scopeType = $scope;
            $scopeId = 0;
        } elseif ($scope == "stores") {
            $scopeType = $scope;
            $scopeId = $this->getRequest()->getParam('storeId');
        } else {
            $scopeType = "websites";
            $scopeId = $this->syncHelper->getCurrentStoreId($scopeType);
        }

        $testConnection = 0; //default Acumatica connection status

        $serverUrl = $this->getRequest()->getParam('serverUrl');
        if (substr($serverUrl, -1) == '/') {
            $serverUrl = $this->getRequest()->getParam('serverUrl');
        } else {
            $serverUrl = $this->getRequest()->getParam('serverUrl') . '/';
        }
        $userName = $this->getRequest()->getParam('userName');
        $password = $this->getRequest()->getParam('password');
        $confirmPassword = $this->getRequest()->getParam('confirmPassword');
        if ($password == '' || $confirmPassword == '') {
            if ($password == '') {
                $password = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/password', $scopeType, $scopeId);
            }
            if ($confirmPassword == '') {
                $confirmPassword = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/confirmPassword', $scopeType, $scopeId);
            }
        }
        $companyName = $this->getRequest()->getParam('companyName');

        $errorFlag = false;
        if (trim($serverUrl) == '' || trim($userName) == '' || trim($password) == '' || trim($confirmPassword) == '' || trim($companyName) == '' || trim($password) != trim($confirmPassword)) {
            $errorFlag = true;
        }
        /**
         * Popup log for test connection initiate
         */

        if (!$errorFlag) {

            $url = $this->urlHelper->getBasicConfigUrl($serverUrl);
            /**
             * Login acumatica
             */

            $acumaticaConnection = $this->clientHelper->login(null, $url,$scopeId);
            if($acumaticaConnection != 0){
                $this->config->saveConfig('amconnectorcommon/amconnectoracucon/serverUrl', $serverUrl, $scopeType, $scopeId);
                $this->config->saveConfig('amconnectorcommon/amconnectoracucon/userName', $userName, $scopeType, $scopeId);
                $this->config->saveConfig('amconnectorcommon/amconnectoracucon/password', $password, $scopeType, $scopeId);
                $this->config->saveConfig('amconnectorcommon/amconnectoracucon/confirmPassword', $confirmPassword, $scopeType, $scopeId);
                $this->config->saveConfig('amconnectorcommon/amconnectoracucon/companyName', $companyName, $scopeType, $scopeId);

                /**
                 * connecting with Acumatica for Default Data
                 */
                $csvWarehouseData = $this->syncHelper->getEnvelopeData('WAREHOUSELIST');
                $warehouseXMLGetRequest = $csvWarehouseData['envelope'];
                $warehouseAction = $csvWarehouseData['envName']."/".$csvWarehouseData['envVersion']."/".$csvWarehouseData['methodName'];
                $getWarehouseResponse = $this->clientHelper->getAcumaticaResponse($warehouseXMLGetRequest,$url, $warehouseAction,$scopeId);
                $warehouseXmlData = $getWarehouseResponse->Body->GetListResponse->GetListResult;
                $warehouseTotalData = $this->xmlHelper->xml2array($warehouseXmlData);
                if(count($warehouseTotalData) > 0){
                    $this->syncResourceModel->deleteWarehouseData($scopeId);
                    if(isset($warehouseTotalData['Entity']['WarehouseID']['Value'])){
                        if($warehouseTotalData['Entity']['Active']['Value'] == 'true') {
                            $warehouseName = trim($warehouseTotalData['Entity']['WarehouseID']['Value']);
                            $this->syncResourceModel->insertWarehouseData($warehouseName, $scopeId);
                        }
                    }else{
                        foreach($warehouseTotalData ['Entity'] as $warehouseData){
                            /* If warehouse is active in acumatica */
                            if($warehouseData->Active->Value == 'true') {
                                $warehouseName = trim($warehouseData->WarehouseID->Value);
                                $this->syncResourceModel->insertWarehouseData($warehouseName, $scopeId);
                            }
                        }
                    }
                    echo "Warehouse Updated successfully";
                }else{
                    echo "Error: Fail to update warehouse";
                }

                /**
                 * Customer Class Details
                 */
                $amconnectorConfigUrl = $this->urlHelper->getBasicConfigUrl($serverUrl);
                $csvCustomerClassData = $this->syncHelper->getEnvelopeData('DEFAULTCUSTOMERCLASS');
                $customerClassXMLGetRequest = $csvCustomerClassData['envelope'];
                $customerClassAction = $csvCustomerClassData['envName']."/".$csvCustomerClassData['envVersion']."/".$csvCustomerClassData['methodName'];
                $getCustomerClassResponse = $this->clientHelper->getAcumaticaResponse($customerClassXMLGetRequest,$amconnectorConfigUrl, $customerClassAction,$scopeId);
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
                    echo "customer class Updated successfully";
                } else {
                    echo "Error: Fail to update customer class";
                }

                /**
                 * Customer Terms Details
                 */
                $csvCustomerTermData = $this->syncHelper->getEnvelopeData('DEFAULTCUSTOMERTERMS');
                $customerTermXMLGetRequest = $csvCustomerTermData['envelope'];
                $customerTermAction = $csvCustomerTermData['envName']."/".$csvCustomerTermData['envVersion']."/".$csvCustomerTermData['methodName'];
                $getCustomerTermResponse = $this->clientHelper->getAcumaticaResponse($customerTermXMLGetRequest,$amconnectorConfigUrl, $customerTermAction,$scopeId);
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
                    echo "customer Term Updated successfully";
                } else {
                    echo "Error: Fail to update customer Term";
                }

                /**
                 * Customer cycle
                 */
                $csvCustomerCycleData = $this->syncHelper->getEnvelopeData('DEFAULTCUSTOMERSTATEMENTCYCLE');
                $customerCycleXMLGetRequest = $csvCustomerCycleData['envelope'];
                $customerCycleAction = $csvCustomerCycleData['envName']."/".$csvCustomerCycleData['envVersion']."/".$csvCustomerCycleData['methodName'];
                $getCustomerCycleResponse = $this->clientHelper->getAcumaticaResponse($customerCycleXMLGetRequest,$amconnectorConfigUrl, $customerCycleAction,$scopeId);
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
                    echo "customer Cycle Updated successfully";
                } else {
                    echo "Error: Fail to update customer Cycle";
                }

                /**
                 * Sales Account
                 */
                $csvSalesAccountData = $this->syncHelper->getEnvelopeData('DEFAULTSALESACCOUNT');
                $salesAccountXMLGetRequest = $csvSalesAccountData['envelope'];
                $salesAccountAction = $csvSalesAccountData['envName']."/".$csvSalesAccountData['envVersion']."/".$csvSalesAccountData['methodName'];
                $getSalesAccountResponse = $this->clientHelper->getAcumaticaResponse($salesAccountXMLGetRequest,$amconnectorConfigUrl, $salesAccountAction,$scopeId);
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
                    echo "sales account Updated successfully";
                } else {
                    echo "Error: Fail to update sales account";
                }

                /**
                 * Payment Method Details
                 */
                $csvPaymentMethodData = $this->syncHelper->getEnvelopeData('DEFAULTPAYMENTMETHOD');
                $paymentMethodXMLGetRequest = $csvPaymentMethodData['envelope'];
                $paymentMethodAction = $csvPaymentMethodData['envName']."/".$csvPaymentMethodData['envVersion']."/".$csvPaymentMethodData['methodName'];
                $getPaymentMethodResponse = $this->clientHelper->getAcumaticaResponse($paymentMethodXMLGetRequest,$amconnectorConfigUrl, $paymentMethodAction,$scopeId);
                $paymentMethodXmlData = $getPaymentMethodResponse->Body->GetListResponse->GetListResult;
                $paymentMethodTotalData = $this->xmlHelper->xml2array($paymentMethodXmlData);
                if(count($paymentMethodTotalData) > 0){
                    $paymentStatus = $this->syncResourceModel->insertPaymentMethodData($paymentMethodXmlData,$scopeId);
                    if($paymentStatus == 0){
                        echo "Payment method updated sucessfully";
                    }else{
                        echo "Failed to upload payment method";
                    }
                }else{
                    echo "Failed to upload payment method";
                }
                $this->_messageManager->addSuccess(__("Acumatica connection successful!"));
                $testConnection = '1';
            }else{
                $testConnection = 0;
                $this->_messageManager->addError(__("Failed to connect with acumatica!"));
            }
        }else{
            $testConnection = 0;
            $this->_messageManager->addError(__("Failed to connect with acumatica!"));
        }
        echo "TEST connection completed";
        return $testConnection;
    }

    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->_check();
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        if ($result == 0) {
            return $resultJson->setData([
                'valid' => 0,
                'message' => 'Test connection is failed, please check the values which you have entered',
            ]);
        } else {
            return $resultJson->setData([
                'valid' => 1,
                'message' => 'Test connection is success.',
            ]);
        }
    }
}
