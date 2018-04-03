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

class SaveRecommended extends \Magento\Backend\App\Action
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
     * @var \Kensium\Amconnector\Model\ResourceModel\Category
     */
    protected $resourceModelCategory;
    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $resourceModelSync;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\CategoryFactory $categoryFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Category $resourceModelCategory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Kensium\Amconnector\Model\CategoryFactory $categoryFactory,
        \Kensium\Amconnector\Model\ResourceModel\Category $resourceModelCategory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Kensium\Amconnector\Model\ResourceModel\Sync $resourceModelSync,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryFactory = $categoryFactory;
        $this->resourceModelCategory = $resourceModelCategory;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->resourceModelSync = $resourceModelSync;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $session = $this->session->getData();
        $gridSessionStoreId = $session['storeId'];
        if ($gridSessionStoreId == 0) {
            $gridSessionStoreId = 1;
            $storeId = 0;
            $scopeType = 'default';
        }else{
            $storeId = $gridSessionStoreId;
            $scopeType = 'stores';
        }

        try {

            $model = $this->categoryFactory->create();
            /**
             * Delete the Mapping Table data first for current store
             */
            $this->resourceModelCategory->deleteMappingTableData($storeId);

            /**
             * 1 => Acumatica to magento
             * 2 => Magento to Acumatica
             * 3 => Bi-Directional
             */
            $directionValue = $this->scopeConfigInterface->getValue('amconnectorsync/categorysync/syncdirection',$scopeType,$storeId);
            if($directionValue == 1){
                $values = 'Acumatica to Magento';
            }elseif($directionValue == 2){
                $values = 'Magento to Acumatica';
            }else{
                $values = 'Bi-Directional (Last Update Wins)';
            }
            /**
             *Here We need set the recommended attribute values
             */
            $recommendedData = array(
                'name'                          =>(array('acumatica_attr_code'=>'Description','sync_direction'=>  $values)),
                'default_sort_by'               =>(array('acumatica_attr_code'=>'ProductSortBy','sync_direction'=>  $values)),
                'description'                   =>(array('acumatica_attr_code'=>'DescriptionLong','sync_direction'=>  $values)),
                'meta_description'              =>(array('acumatica_attr_code'=>'MetaDescription','sync_direction'=>  $values)),
                'meta_keywords'                 =>(array('acumatica_attr_code'=>'MetaKeywords','sync_direction'=>  $values)),
                'meta_title'                    =>(array('acumatica_attr_code'=>'MetaTitle','sync_direction'=>  $values)),
                'url_key'                       =>(array('acumatica_attr_code'=>'URLKey','sync_direction'=>  $values)),
                'include_in_menu'               =>(array('acumatica_attr_code'=>'IncludeinNavigationMenu','sync_direction'=> $values)),
                'acumatica_parent_category_id'  =>(array('acumatica_attr_code'=>'ParentCategory','sync_direction'=> 'Acumatica to Magento')),
                'acumatica_category_id'         =>(array('acumatica_attr_code'=>'Category','sync_direction'=> 'Acumatica to Magento'))
            );
            foreach ($recommendedData as $key => $value) {
                if ($key == '') continue;
                $value['magento_attr_code'] = $key;
                $model = $this->categoryFactory->create();
                $model = $model->setData($value);
                $model->setStoreId($gridSessionStoreId);
                $model->save();
            }
            $this->messageManager->addSuccess("Recommended Category attributes mapped successfully");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();

        } catch (Exception $e) {
            $this->messageManager->addError("Error occurred while mapping Category attribute");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();

        }
    }
}
