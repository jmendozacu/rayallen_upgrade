<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Customermapping;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SaveSchema extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Kensium\Amconnector\Helper\Client
     */
    protected $clientHelper;

    /**
     * @var \Kensium\Amconnector\Helper\Customer
     */
    protected $amconnectorCustomerHelper;

    /**
     * @var \Kensium\Amconnector\Helper\Url
     */
    protected $urlHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Customer
     */
    protected $customerResourceModel;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Helper\Client $clientHelper
     * @param \Kensium\Amconnector\Helper\Url $urlHelper
     * @param \Kensium\Amconnector\Helper\Customer $amconnectorCustomerHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Customer $customerResourceModel
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfigInterface,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Kensium\Amconnector\Helper\Customer $amconnectorCustomerHelper,
        \Kensium\Amconnector\Model\ResourceModel\Customer $customerResourceModel,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->clientHelper = $clientHelper;
        $this->amconnectorCustomerHelper = $amconnectorCustomerHelper;
        $this->urlHelper = $urlHelper;
        $this->customerResourceModel = $customerResourceModel;
        $this->scopeConfigInterface = $scopeConfigInterface;
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
            if($storeId == 0){
                $scopeType = 'default';
            }else{
                $scopeType = 'stores';
            }
            $customerSchemaUrl = $this->urlHelper->getSchemaUrl($storeId);
            if(isset($customerSchemaUrl) && $customerSchemaUrl != '')
            {
                $schemaData = $this->amconnectorCustomerHelper->getCustomerSchema($customerSchemaUrl, $storeId);
                $this->customerResourceModel->updateCustomerSchema($schemaData, $storeId);
                $this->messageManager->addSuccess("Customer schema added/updated successfully");
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
