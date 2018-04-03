<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\System\Config;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;

class GetSalesAccount extends \Magento\Backend\App\Action
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
     * @param \Magento\Framework\App\Request\Http $request
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
        \Magento\Framework\App\Request\Http $request,
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
        $this->request = $request;
        $this->_messageManager = $context->getMessageManager();
        $this->baseDirPath = $baseDirPath;
        $this->syncResourceModel = $syncResourceModel;
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

        $getSalesAccountStatus = 0;

        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',$scopeType,$scopeId);
        $amconnectorConfigUrl = $this->urlHelper->getBasicConfigUrl($serverUrl);
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
            $this->_messageManager->addSuccess(__("Sales account data updated successfully!"));
            $getSalesAccountStatus = 1;
        } else {
            $this->_messageManager->addError(__("Failed to update sales account data!"));
            $getSalesAccountStatus = 0;
        }

        return $getSalesAccountStatus;
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
                'message' => 'Failed to update sales account',
            ]);
        } else {
            return $resultJson->setData([
                'valid' => 1,
                'message' => 'Default sales account updated successfully',
            ]);
        }
    }
}
