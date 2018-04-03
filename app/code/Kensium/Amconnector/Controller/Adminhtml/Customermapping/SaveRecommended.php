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
     * @var \Kensium\Amconnector\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Customer
     */
    protected $resourceModelCustomer;
    
    protected $scopeConfigInterface;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\CustomerFactory $customerFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Customer $resourceModelCustomer
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Kensium\Amconnector\Model\CustomerFactory $customerFactory,
        \Kensium\Amconnector\Model\ResourceModel\Customer $resourceModelCustomer,
        PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    )
    {
        parent::__construct($context);
        $this->session = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->customerFactory = $customerFactory;
        $this->resourceModelCustomer = $resourceModelCustomer;
        $this->scopeConfigInterface = $scopeConfigInterface;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $session = $this->session->getData();
        /*$gridSessionStoreId = $session['storeId'];
        if ($gridSessionStoreId == 0) {
            $gridSessionStoreId = 1;
        }*/
        $storeId = $this->getRequest()->getParam('store');
        try {
  
            $model = $this->customerFactory->create();
            /**
             * Truncating the Mapping Table first
             */
            $this->resourceModelCustomer->truncateMappingTable($storeId);
            /**
             *Here We need set the recommended attribute values
             */

            $syncDirection = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/syncdirection', \Kensium\Amconnector\Helper\Url::SCOPE_TYPE,$storeId);
            if($syncDirection == 1){
                $values = 'Acumatica to Magento';
            }elseif($syncDirection == 2){
                $values = 'Magento to Acumatica';
            }else{
                $values = 'Bi-Directional (Last Update Wins)';
            }
            
            $recommendedData = array(
                'firstname' => (array('acumatica_attr_code' => 'CustomerName', 'sync_direction' => $values)),
                'lastname' => (array('acumatica_attr_code' => 'CustomerName', 'sync_direction' => $values)),
                'email' => (array('acumatica_attr_code' => 'Email', 'sync_direction' => $values)),
                'BILLADD_firstname' => (array('acumatica_attr_code' => 'CustomerName', 'sync_direction' => $values)),
                'BILLADD_lastname' => (array('acumatica_attr_code' => 'CustomerName', 'sync_direction' => $values)),
                'BILLADD_company' => (array('acumatica_attr_code' => 'CompanyName', 'sync_direction' => $values)),
                'BILLADD_street' => (array('acumatica_attr_code' => 'AddressLine1', 'sync_direction' => $values)),
                'BILLADD_city' => (array('acumatica_attr_code' => 'City', 'sync_direction' => $values)),
                'BILLADD_country_id' => (array('acumatica_attr_code' => 'Country', 'sync_direction' => $values)),
                'BILLADD_region' => (array('acumatica_attr_code' => 'State', 'sync_direction' => $values)),
                'BILLADD_region_id' => (array('acumatica_attr_code' => 'State', 'sync_direction' => $values)),
                'BILLADD_postcode' => (array('acumatica_attr_code' => 'PostalCode', 'sync_direction' => $values)),
                'BILLADD_telephone' => (array('acumatica_attr_code' => 'Phone1', 'sync_direction' => $values)),
                'BILLADD_fax' => (array('acumatica_attr_code' => 'Fax', 'sync_direction' => $values)),
                'acumatica_customer_id' => (array('acumatica_attr_code' => 'CustomerID', 'sync_direction' => $values))
            );
            foreach ($recommendedData as $key => $value) {
                if ($key == '') continue;
                $value['magento_attribute_id'] = $this->resourceModelCustomer->getCustomerAttributeId($key);
                if($value['magento_attribute_id'] == '') continue;
                $value['magento_attr_code'] = $key;
                $model = $this->customerFactory->create();
                $model = $model->setData($value);
                $model->setStoreId($storeId);
                $model->save();
            }
            $this->messageManager->addSuccess("Recommended Customer attributes mapped successfully");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();

        } catch (Exception $e) {
            $this->messageManager->addError("Error occurred while mapping Customer attribute");
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();

        }


    }

}
