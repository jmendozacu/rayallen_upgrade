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

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Kensium\Amconnector\Model\ShipFactory
     */
    protected $shipFactory;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\ShipFactory $shipFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Kensium\Amconnector\Model\ShipFactory $shipFactory
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->shipFactory = $shipFactory;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        try {
            $model = $this->shipFactory->create();
            $session = $this->session->getData();
            $gridSessionData = $session['gridData'];
            $gridSessionStoreId = $session['storeId'];
            if ($gridSessionStoreId == 0) {
                $gridSessionStoreId = 1;
            }
            foreach ($gridSessionData as $key => $value) {
                if ($key == '') continue;
                $value['magento_attr_code'] = substr($key, 0, strrpos($key, '|'));
                $value['carrier'] = str_replace('|', '', strstr($key, '|'));
                $modelColl = $model->getCollection()->addFieldToFilter('magento_attr_code', $value['magento_attr_code'])->addFieldToFilter('store_id', $gridSessionStoreId);
                $modelCollData = $modelColl->getData();
                if (!empty($modelCollData)) {
                    foreach ($modelColl as $model) {
                        $model->setAcumaticaAttrCode($value['acumatica_attr_code']);
                        $model->setStoreId($gridSessionStoreId);
                        $model->setCarrier($value['carrier']);
                        $model->setId($modelCollData[0]['id']);
                        $model->save();
                    }
                } else {
                    $model = $this->shipFactory->create();
                    $model = $model->setData($value);
                    $model->setStoreId($gridSessionStoreId);
                    $model->save();
                }
            }
            $this->messageManager->addSuccess("Shipping method(s) saved successfully.");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();

        } catch (Exception $e) {
            $this->messageManager->addError("Error occurred while mapping shipping method");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }

    }

}
