<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Payment;

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
     * @var \Kensium\Amconnector\Helper\Payment
     */
    protected $paymentHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\payment
     */
    protected $paymentResourceModel;


    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Helper\Payment $paymentHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Payment $paymentResourceModel
     */
    public function __construct(
        Context $context,
        \Kensium\Amconnector\Helper\Url $url,
        \Kensium\Amconnector\Helper\Payment $paymentHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Kensium\Amconnector\Model\ResourceModel\Payment $paymentResourceModel
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->paymentHelper = $paymentHelper;
        $this->url = $url;
        $this->paymentResourceModel = $paymentResourceModel;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
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
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl','stores',$storeId);
            if(!isset($serverUrl)){
                $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
            }
            if(isset($serverUrl) && $serverUrl != '') {
                $paymentSchemaUrl = $this->url->getBasicConfigUrl($serverUrl);
                $schemaData = $this->paymentHelper->getPaymentSchema($paymentSchemaUrl, $storeId);
                $this->paymentResourceModel->updatePaymentSchema($schemaData, $storeId);
                $this->messageManager->addSuccess("Payment Method schema added/updated successfully");
            }else{
                $this->messageManager->addError("Test Connection Failed.");
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        } catch (SoapFault $e) {
            echo $e->getMessage();
        }

    }

}
