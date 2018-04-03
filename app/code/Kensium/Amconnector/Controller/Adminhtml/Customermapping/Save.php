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

class Save extends \Magento\Backend\App\Action
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
     * @var \Kensium\Amconnector\Model\CustomerFactory
     */
    protected $customerFactory;

    protected $resourceModelAmconnectorCustomer;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\CustomerFactory $customerFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Kensium\Amconnector\Model\CustomerFactory $customerFactory,
        \Kensium\Amconnector\Model\ResourceModel\Customer $resourceModelAmconnectorCustomer,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->resourceModelAmconnectorCustomer = $resourceModelAmconnectorCustomer;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $model = $this->customerFactory->create();
            $session = $this->session->getData();
            $gridSessionStoreId = '';
            $gridSessionData = array();
            if (isset($session['gridData']))
                $gridSessionData = $session['gridData'];
            if (isset($session['storeId']))
                $gridSessionStoreId = $session['storeId'];
            if ($gridSessionStoreId == 0) {
                $gridSessionStoreId = 1;
            }
            foreach ($gridSessionData as $key => $value) {
                if ($key == '') continue;
                $value['magento_attr_code'] = $key;
                $value['magento_attribute_id'] = $this->resourceModelAmconnectorCustomer->getCustomerAttributeId($key);
                if($value['magento_attribute_id'] == '') continue;
                $modelColl = $model->getCollection()->addFieldToFilter('magento_attr_code', $key)
                    ->addFieldToFilter('store_id', $gridSessionStoreId);
                $modelCollData = $modelColl->getData();
                foreach ($modelCollData as $cols) {
                    $colsValue = $cols['id'];
                }
                if (isset($colsValue))
                    $model = $model->load($colsValue);

                if (!empty($modelCollData)) {
                    foreach ($modelColl as $model) {
                        if (isset($value['acumatica_attr_code']) ){
                            $model->setAcumaticaAttrCode($value['acumatica_attr_code']);
			    $model->setMagentoAttributeId($value['magento_attribute_id']);
			}
                        if(isset($value['sync_direction']))
                            $model->setSyncDirection($value['sync_direction']);
                            
                        $model->setStoreId($gridSessionStoreId);
                        $model->save();
                    }
                } else {
                    $model = $this->customerFactory->create();
                    $model = $model->setData($value);
                    $model->setStoreId($gridSessionStoreId);
                    $model->save();
                }
            }
            $this->messageManager->addSuccess("Customer attributes Mapped successfully");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();//->setPath('amconnector/customer/index/',array("store" => $gridSessionStoreId));

        } catch (Exception $e) {
            $this->messageManager->addError("Error occurred while mapping Product attribute");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();//->setPath('amconnector/customer/index/',array("store" => $gridSessionStoreId));
        }
    }

}
