<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Kensium\Amconnector\Model\OrderFactory;
use Magento\Backend\Model\Session;
use Magento\Framework\Message\ManagerInterface;

class Save extends Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Order
     */
    protected $orderFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Context $context
     * @param OrderFactory $orderFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->orderFactory = $orderFactory;
        $this->messageManager = $context->getMessageManager();
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $model = $this->orderFactory->create();
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
                $modelColl = $model->getCollection()->addFieldToFilter('magento_attr_code', $key)->addFieldToFilter('store_id', $gridSessionStoreId);
                $modelCollData = $modelColl->getData();
                if (isset($modelCollData[0]['id']))
                    $model = $this->orderFactory->create()->load($modelCollData[0]['id']);
                if ($modelCollData) {
                    foreach ($modelColl as $model) {
                        if(isset($value['acumatica_attr_code']))
                        $model->setAcumaticaAttrCode($value['acumatica_attr_code']);
                        $model->setStoreId($gridSessionStoreId);
                        $model->save();
                    }
                } else {
                    $model = $this->orderFactory->create();
                    $model = $model->setData($value);
                    $model->setStoreId($gridSessionStoreId);
                    $model->save();
                }
            }

            $this->messageManager->addSuccessMessage("Order Status attributes Mapped successfully");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();

        } catch (Exception $e) {
            $this->messageManager->addError(Mage::helper("adminhtml")->__("Error occurred while mapping Product attribute"));
            return $resultRedirect->setRefererOrBaseUrl();
        }
    }

}
