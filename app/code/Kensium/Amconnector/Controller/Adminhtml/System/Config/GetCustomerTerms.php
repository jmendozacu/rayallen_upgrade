<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\System\Config;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;

class GetCustomerTerms extends \Magento\Backend\App\Action
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
        $this->request = $request;
        $this->_messageManager = $context->getMessageManager();
        $this->urlHelper = $urlHelper;
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

        $getCustomerTermsStatus = 0;

        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',$scopeType,$scopeId);
        $amconnectorConfigUrl = $this->urlHelper->getBasicConfigUrl($serverUrl);
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
            $this->_messageManager->addSuccess(__("Customer terms data updated successfully!"));
            $getCustomerTermsStatus = 1;
        } else {
            $this->_messageManager->addError(__("Failed to update customer terms data!"));
            $getCustomerTermsStatus = 0;
        }

        return $getCustomerTermsStatus;
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
                'message' => 'Failed to update customer terms',
            ]);
        } else {
            return $resultJson->setData([
                'valid' => 1,
                'message' => 'Default customer terms updated successfully',
            ]);
        }
    }
}
