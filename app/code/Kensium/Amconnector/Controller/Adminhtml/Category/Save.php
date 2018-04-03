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
     * @var \Kensium\Amconnector\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\CategoryFactory $categoryFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Kensium\Amconnector\Model\CategoryFactory $categoryFactory,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $model = $this->categoryFactory->create();
            $session = $this->session->getData();

            $gridSessionData = $session['gridData'];
            $gridSessionStoreId = $session['storeId'];
            if($gridSessionStoreId == 0){
                $gridSessionStoreId = 1;
            }

            foreach ($gridSessionData as $key => $value) {

                if ($key == '') continue;
                $value['magento_attr_code'] = $key;
                $modelColl = $model->getCollection()->addFieldToFilter('magento_attr_code', $key)
                    ->addFieldToFilter('store_id', $gridSessionStoreId);

                $modelCollData = $modelColl->getData();
                foreach ($modelCollData as $cols) {
                    $cols['id'];
                }

                if (isset($cols['id']))
                    $model = $model->load($cols['id']);

                if (!empty($modelCollData)) {
                    foreach ($modelColl as $model) {
                        if(isset($value['acumatica_attr_code'])){
                            $model->setAcumaticaAttrCode($value['acumatica_attr_code']);
                        }
                        if(isset($value['sync_direction'])){
                            $model->setSyncDirection($value['sync_direction']);
                        }
                        $model->setStoreId($gridSessionStoreId);
                        $model->save();
                    }
                } else {
                    $model = $this->categoryFactory->create();
                    $model = $model->setData($value);
                    $model->setStoreId($gridSessionStoreId);
                    $model->save();
                }
            } 
            $this->messageManager->addSuccess("Category attributes Mapped successfully");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();

        } catch (Exception $e) {
            $this->messageManager->addError("Error occurred while mapping Category attribute");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }
    }

}
