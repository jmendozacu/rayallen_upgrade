<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\System\Config;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;

class GetWarehouse extends \Magento\Backend\App\Action
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
     * @var \Kensium\Amconnector\Helper\Client
     */
    protected $clientHelper;
    /**
     * @var \Kensium\Amconnector\Helper\Xml
     */
    protected $xmlHelper;
    /**
     * @var \Kensium\Amconnector\Helper\Url
     */
    protected $urlHelper;
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
    protected $amconnectorObserver;
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
     * @param \Kensium\Amconnector\Observer $amconnectorObserver
     * @param \Magento\Framework\App\Request\Http $request
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
        \Kensium\Amconnector\Model\ResourceModel\Sync   $syncResourceModel,
        \Kensium\Amconnector\Observer $amconnectorObserver,
        \Magento\Framework\App\Request\Http $request
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
        $this->amconnectorObserver = $amconnectorObserver;
        $this->request = $request;
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
            $scopeId = $this->getRequest()->getParam('store');
        } else {
            $scopeType = "websites";
            $scopeId = $this->syncHelper->getCurrentStoreId($scopeType);
        }
        $getWarehouseStatus = 0;
        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',$scopeType,$scopeId);
        $url = $this->urlHelper->getBasicConfigUrl($serverUrl);
        /**
         * connecting with Acumatica for Default Data
         */
        $csvWarehouseData = $this->syncHelper->getEnvelopeData('WAREHOUSELIST');
        $warehouseXMLGetRequest = $csvWarehouseData['envelope'];
        $warehouseAction = $csvWarehouseData['envName'] . "/" . $csvWarehouseData['envVersion'] . "/" . $csvWarehouseData['methodName'];
        $getWarehouseResponse = $this->clientHelper->getAcumaticaResponse($warehouseXMLGetRequest, $url, $warehouseAction,$scopeId);
        $warehouseXmlData = $getWarehouseResponse->Body->GetListResponse->GetListResult;
        $warehouseTotalData = $this->xmlHelper->xml2array($warehouseXmlData);
        if (count($warehouseTotalData) > 0) {
            $this->syncResourceModel->deleteWarehouseData($scopeId);
            if (isset($warehouseTotalData['Entity']['WarehouseID']['Value'])) {
                if ($warehouseTotalData['Entity']['Active']['Value'] == 'true') {
                    $warehouseName = trim($warehouseTotalData['Entity']['WarehouseID']['Value']);
                    $this->syncResourceModel->insertWarehouseData($warehouseName, $scopeId);
                }
            } else {
                foreach ($warehouseTotalData ['Entity'] as $warehouseData) {
                    /* If warehouse is active in acumatica */
                    if (strtolower($warehouseData->Active->Value) == 'true') {
                        $warehouseName = trim($warehouseData->WarehouseID->Value);
                        $this->syncResourceModel->insertWarehouseData($warehouseName, $scopeId);
                    }
                }
            }
            $this->_messageManager->addSuccess(__("Warehouse data updated successfully!"));
            $getWarehouseStatus = 1;
        } else {
            $this->_messageManager->addError(__("Failed to update warehouse data!"));
            $getWarehouseStatus = 0;
        }
        return $getWarehouseStatus;
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
                'message' => 'Failed to update warehouse',
            ]);
        } else {
            return $resultJson->setData([
                'valid' => 1,
                'message' => 'Default warehouse updated successfully',
            ]);
        }
    }
}
