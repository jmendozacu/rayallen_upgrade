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
     * @var \Kensium\Amconnector\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Product
     */
    protected $resourceModelProduct;

    /**
     * @var  \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\ProductFactory $productFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Kensium\Amconnector\Model\ProductFactory $productFactory,
        \Kensium\Amconnector\Model\ResourceModel\Product $resourceModelProduct,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->eavAttribute = $eavAttribute;
        $this->session = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->productFactory = $productFactory;
        $this->resourceModelProduct = $resourceModelProduct;
        $this->scopeConfigInterface = $scopeConfigInterface;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store');
        if ($storeId == 0) {
           $storeId = 1;
        }
        try {

            $model = $this->productFactory->create();
            /**
             * Truncating the Mapping Table first
             */
            $this->resourceModelProduct->truncateMappingTable($storeId);
            /**
             *Here We need set the recommended attribute values
             */
            $checkFeature = $this->eavAttribute->loadByCode('catalog_product','is_featured');
            $syncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/productsync/syncdirection', \Kensium\Amconnector\Helper\Url::SCOPE_TYPE,$storeId);
            if($syncDirection == 1){
                $values = 'Acumatica to Magento';
            }elseif($syncDirection == 2){
                $values = 'Magento to Acumatica';
            }else{
                $values = 'Bi-Directional (Last Update Wins)';
            }
             $recommendedData = array(
                    'tax_class_id'      =>(array('acumatica_attr_code'=>'TaxCategory','sync_direction'=>$values)),
                    'description'       =>(array('acumatica_attr_code'=>'DescriptionLong','sync_direction'=>$values)),
                    'meta_description'  =>(array('acumatica_attr_code'=>'MetaDescription','sync_direction'=>$values)),
                    'meta_keyword'      =>(array('acumatica_attr_code'=>'MetaKeywords','sync_direction'=>$values)),
                    'meta_title'        =>(array('acumatica_attr_code'=>'MetaTitle','sync_direction'=>$values)),
                    'name'              =>(array('acumatica_attr_code'=>'Description','sync_direction'=>$values)),
                    'price'             =>(array('acumatica_attr_code'=>'DefaultPrice','sync_direction'=>$values)),
                    'short_description' =>(array('acumatica_attr_code'=>'DescriptionShort','sync_direction'=>$values)),
                    'sku'               =>(array('acumatica_attr_code'=>'InventoryID','sync_direction'=>$values)),
                    'status'            =>(array('acumatica_attr_code'=>'Active','sync_direction'=>$values)),
                    'visibility'        =>(array('acumatica_attr_code'=>'Visibility','sync_direction'=>$values)),
                    'weight'            =>(array('acumatica_attr_code'=>'Weight','sync_direction'=>$values))
                );
	   if ($checkFeature->getId()) {
		$featuredArray = array('is_featured' =>(array('acumatica_attr_code'=>'Featured','sync_direction'=>$values)));
	       array_merge($recommendedData,$featuredArray);
            }
            foreach ($recommendedData as $key => $value){
                if($key == '') continue;
                $value['magento_attr_code'] = $key;
                $model = $this->productFactory->create();
                $model = $model->setData($value);
                $model->setStoreId($storeId);
                $model->save();
            }
            $this->messageManager->addSuccess("Recommended Product attributes mapped successfully");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();

        } catch (Exception $e) {
            $this->messageManager->addError("Error occurred while mapping Product attribute");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();

        }


    }

}
