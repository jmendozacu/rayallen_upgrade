<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Ship;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SaveSchema extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Kensium\Amconnector\Helper\Shipment
     */
    protected $shipmentHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Ship
     */
    protected $shipResourceModel;


    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Helper\Shipment $shipmentHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Ship $shipResourceModel
     */
    public function __construct(
        Context $context,
        \Kensium\Amconnector\Helper\Url $url,
        ScopeConfigInterface $scopeConfigInterface,
        \Kensium\Amconnector\Helper\Shipment $shipmentHelper,
        \Kensium\Amconnector\Model\ResourceModel\Ship $shipResourceModel
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->urlHelper = $url;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->shipmentHelper = $shipmentHelper;
        $this->shipResourceModel = $shipResourceModel;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        /**
         * To Get the schema from Acumatica and add/update in magento mapping table
         */

        try {
            $storeId = $this->getRequest()->getParam('store');
            if ($storeId == 0) {
                $storeId = 1;
            }
            $cookies = array();
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl', 'stores', $storeId);
            if(!isset($serverUrl) && $storeId==1){
                $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
            }
            if(isset($serverUrl) && $serverUrl != '')
            {
                $shipViaSchemaUrl = $this->urlHelper->getBasicConfigUrl($serverUrl);
                $schemaData = $this->shipmentHelper->getShipmentSchema($shipViaSchemaUrl, $storeId);
                $this->shipResourceModel->updateShipSchema($schemaData, $storeId);
                $this->messageManager->addSuccess("Ship via code schema added/updated successfully");
            }else{
                $this->messageManager->addError("Test Connection Failed.");
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();

        } catch (SoapFault $e) {
            $e->getMessage();
        }

    }

}

