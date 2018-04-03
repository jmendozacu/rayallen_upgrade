<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

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
     * @var \Kensium\Amconnector\Helper\Category
     */
    protected $categoryHelper;

    /**
     * @var \Kensium\Amconnector\Helper\Url
     */
    protected $urlHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $syncResourceModel;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Helper\Url $urlHelper
     * @param \Kensium\Amconnector\Helper\Category $categoryHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
    Context $context, \Kensium\Amconnector\Helper\Url $urlHelper, \Kensium\Amconnector\Helper\Category $categoryHelper, \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel, PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryHelper = $categoryHelper;
        $this->urlHelper = $urlHelper;
        $this->syncResourceModel = $syncResourceModel;
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
            $cookies = array();
            $storeId = $this->getRequest()->getParam('store');
            if ($storeId == 0) {
                $storeId = 1;
            }
            $categorySchemaUrl = $this->urlHelper->getSchemaUrl($storeId);
            if (isset($categorySchemaUrl) && $categorySchemaUrl != '') {
                $schemaData = $this->categoryHelper->getCategorySchema($categorySchemaUrl, $storeId);
                $this->syncResourceModel->updateCategorySchema($schemaData, $storeId);
                $this->messageManager->addSuccess("Category schema added/updated successfully");
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
