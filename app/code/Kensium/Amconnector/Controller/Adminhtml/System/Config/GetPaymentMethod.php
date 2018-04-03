<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\System\Config;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;
use Kensium\Lib;
use Kensium\Amconnector\Helper\Data;

class GetPaymentMethod extends \Magento\Backend\App\Action
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
     * @var \Kensium\Amconnector\Helper\Xml
     */
    protected $xmlHelper;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $baseDirPath;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
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
     * @param \Kensium\Amconnector\Helper\Xml $xmlHelper
     * @param \Magento\Framework\App\Request\Http $request
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
        \Kensium\Amconnector\Helper\Xml $xmlHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Filesystem\DirectoryList $baseDirPath,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        Lib\Common $common,
        \Kensium\Amconnector\Helper\Data $amconnectorHelper
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->syncHelper = $syncHelper;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->config = $config;
        $this->amconnectorConnection = $amconnectorConnection;
        $this->xmlHelper = $xmlHelper;
        $this->request = $request;
        $this->_messageManager = $context->getMessageManager();
        $this->baseDirPath = $baseDirPath;
        $this->syncResourceModel = $syncResourceModel;
        $this->common = $common;
        $this->amconnectorHelper = $amconnectorHelper;
        
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
            $scopeId = $this->getRequest()->getParam('store');
        } else {
            $scopeType = "websites";
            $scopeId = $this->syncHelper->getCurrentStoreId($scopeType);
        }

        $getPaymentMethodStatus = 0;

        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',$scopeType,$scopeId);
        $amconnectorConfigUrl = $this->common->getBasicConfigUrl($serverUrl);
        /**
         * Payment Method Details
         */
        $csvPaymentMethodData = $this->common->getEnvelopeData('DEFAULTPAYMENTMETHOD');
        $paymentMethodXMLGetRequest = $csvPaymentMethodData['envelope'];
        $paymentMethodAction = $csvPaymentMethodData['envName']."/".$csvPaymentMethodData['envVersion']."/".$csvPaymentMethodData['methodName'];
        $configParameters = $this->amconnectorHelper->getConfigParameters($scopeId);
        $getPaymentMethodResponse = $this->common->getAcumaticaResponse($configParameters,$paymentMethodXMLGetRequest,$amconnectorConfigUrl, $paymentMethodAction);
        $paymentMethodXmlData = array();
        if(isset($getPaymentMethodResponse->Body->GetListResponse->GetListResult)){
          $paymentMethodXmlData = $getPaymentMethodResponse->Body->GetListResponse->GetListResult;  
        }
        $paymentMethodTotalData = $this->xmlHelper->xml2array($paymentMethodXmlData);
        if(count($paymentMethodTotalData) > 0){
            $paymentStatus = $this->syncResourceModel->insertPaymentMethodData($paymentMethodXmlData,$scopeId);
            if($paymentStatus == 0){
                $this->_messageManager->addSuccess(__("Payment method data updated successfully!"));
                $getPaymentMethodStatus = 1;
            }else{
                $this->_messageManager->addError(__("Failed to update payment method data!"));
                $getPaymentMethodStatus = 0;
            }
        }else{
            $this->_messageManager->addError(__("Failed to update payment method data!"));
            $getPaymentMethodStatus = 0;
        }

        return $getPaymentMethodStatus;
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
                'message' => 'Failed to update payment method',
            ]);
        } else {
            return $resultJson->setData([
                'valid' => 1,
                'message' => 'Default payment method updated successfully',
            ]);
        }
    }
}
