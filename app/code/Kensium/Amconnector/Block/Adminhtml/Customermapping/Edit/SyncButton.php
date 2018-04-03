<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\Customermapping\Edit;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveAndContinueButton
 */
class SyncButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;
    /**
     * @var
     */
    protected $storeManagerInterface;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param AccountManagementInterface $customerAccountManagement
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        AccountManagementInterface $customerAccountManagement
    ) {
        $this->storeManagerInterface = $context->getStoreManager();
        $this->scopeConfigInterface = $context->getScopeConfig();
        parent::__construct($context, $registry);
        $this->customerAccountManagement = $customerAccountManagement;

    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $customerId = $this->getCustomerId();
        $storeCode = $this->storeManagerInterface->getStore()->getCode();
        $store_id = $this->storeManagerInterface->getStore($storeCode)->getId();
        $websiteCode = $this->storeManagerInterface->getWebsite()->getCode();
        if($store_id == 0){
            $scope = 'default';
            $storeId = 1;
        }else{
            $scope = 'stores';
            $storeId = $store_id;
        }
        $individualSyncEnable = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/individualsync',$scope,$storeId);
        if(!isset($individualSyncEnable))
            $individualSyncEnable = $this->scopeConfigInterface->getValue('amconnectorsync/customersync/individualsync');
        $data = [];
        if ($this->getCustomerId() && $individualSyncEnable == 1) {
            $data = [
                'label' => __('Sync Now'),
                'class' => 'save primary',
                'on_click' => "syncNow('".$this->syncNowUrl()."customer_id/".$customerId."')",
                'sort_order' => 100,
            ];
        }
        return $data;
    }

    /**
     * @param array $args
     * @return string
     */
    public function syncNowUrl(array $args = [])
    {
        $params = array_merge($this->getDefaultUrlParams(), $args);
        return $this->getUrl('amconnector/sync/individualCustomer', $params);
    }

    /**
     * @return array
     */
    protected function getDefaultUrlParams()
    {
        return ['_current' => true, '_query' => ['isAjax' => null]];
    }

}
