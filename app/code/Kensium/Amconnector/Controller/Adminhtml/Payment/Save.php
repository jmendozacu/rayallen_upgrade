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
     * @var \Kensium\Amconnector\Model\PaymentFactory
     */
    protected $paymentFactory;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\PaymentFactory $Factory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Kensium\Amconnector\Model\PaymentFactory $paymentFactory,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->paymentFactory = $paymentFactory;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $model = $this->paymentFactory->create();
            $session = $this->session->getData();
            $gridSessionData = $session['gridData'];
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
                    $model = $this->paymentFactory->create()->load($modelCollData[0]['id']);
                if ($modelCollData) {
                    foreach ($modelColl as $model) {
                        if(isset($value['acumatica_attr_code']))
                        $model->setAcumaticaAttrCode($value['acumatica_attr_code']);
                        if(isset($value['cash_account']))
                        $model->setCashAccount($value['cash_account']);
                        $model->setStore_id($gridSessionStoreId);
                        //$model->setId($modelCollData[0]['id']);
                        $model->save();
                    }
                } else {
                    $model = $this->paymentFactory->create();
                    $model = $model->setData($value);
                    $model->setStoreId($gridSessionStoreId);
                    $model->save();
                }
            }
            $this->messageManager->addSuccess("Payment method saved successfully");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();

        } catch (Exception $e) {
            $this->messageManager("Error occurred while mapping Payment method");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }

    }

}
