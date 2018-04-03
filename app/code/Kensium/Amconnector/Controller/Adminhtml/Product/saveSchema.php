<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;


class saveSchema extends \Magento\Backend\App\Action
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
     * @var \Kensium\Amconnector\Helper\Product
     */
    protected $amconnectorProductHelper;

    /**
     * @var \Kensium\Amconnector\Helper\Url
     */
    protected $urlHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Product
     */
    protected $productResourceModel;


    public function __construct(
        Context $context,
        \Kensium\Amconnector\Helper\Client $clientHelper,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Kensium\Amconnector\Helper\Product $amconnectorProductHelper,
        \Kensium\Amconnector\Model\ResourceModel\Product $productResourceModel,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->clientHelper = $clientHelper;
        $this->amconnectorProductHelper = $amconnectorProductHelper;
        $this->urlHelper = $urlHelper;
        $this->productResourceModel = $productResourceModel;
    }



    public function execute()
    {

        try {
            $storeId = $this->getRequest()->getParam('store');
            if ($storeId == 0) {
              $storeId = 1;
            } 
            $cookies = array();
            $productSchemaUrl = $this->urlHelper->getSchemaUrl($storeId);
            if (isset($productSchemaUrl) && $productSchemaUrl != '') {
                $schemaData = $this->amconnectorProductHelper->getProductSchema($productSchemaUrl, $storeId);
                $this->productResourceModel->updateProductSchema($schemaData, $storeId);
                $this->messageManager->addSuccess("product schema added/updated successfully");
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
