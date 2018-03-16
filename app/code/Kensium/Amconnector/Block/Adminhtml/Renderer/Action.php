<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Renderer;

use Magento\Framework\DataObject;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var
     */
    protected $syncResourceModel;
    /**
     * @var
     */
    protected $licenseResourceModel;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Kensium\Amconnector\Model\SyncFactory $syncFactory
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\Helper\Data $backendHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel,
        \Kensium\Amconnector\Model\ResourceModel\Licensecheck $licenseResourceModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->backendHelper = $backendHelper;
        $this->syncResourceModel = $syncResourceModel;
        $this->storeManager = $storeManager;
        $this->licenseResourceModel = $licenseResourceModel;
    }


    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $action = $jobCode = $filterCode = $row->getData('code');
        $filterUrl = $this->backendHelper->getUrl('scheduler/scheduler/index/filtercode/' . $filterCode) ;
        $storeId = $row->getData('store_id');
        $licenseType = $this->licenseResourceModel->checkLicenseTypes($storeId);
        if($licenseType == 'trial'){
            $trial = '';
        }else{
            $trial = '';
        }
        $url = $this->backendHelper->getUrl('*/*/' . $action);// function define in amconnector.js
        $pubStaticUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC);
        $title = $row->getData('title');
        $tobeInsertedId = $this->syncResourceModel->beforeCheckConnectionFlag($row->getData('id'), $jobCode);
        $logViewUrl = $this->backendHelper->getUrl('*/log/' . $action . '/tobeInsertedId/' . $tobeInsertedId . '/syncId/' . $row->getData('id') . '/code/' . $action);
        $baseUrl = $this->storeManager->getStore($storeId)->getBaseUrl();
        if(strstr($baseUrl,'index.php')){
            $baseUrl =  str_replace("index.php/","",$baseUrl);
        }
        /*if($row->getData('code') == 'productImage') {
            $url = "".$baseUrl . 'imageconnector/createImage.php/?syncId='.$row->getData('id').'&store='.$storeId;
        }*/
        $hiddenData = '<input type="hidden" value="' . $logViewUrl . '" class="logUrl" />';
        $hiddenData .= '<input type="hidden" value="' . $url . '" class="url" />';
        $hiddenData .= '<input type="hidden" value="' . $filterUrl . '" class="filterUrl" />';
        $hiddenData .= '<input type="hidden" value="' . $pubStaticUrl . '" class="baseurl" />';
        $hiddenData .= '<input type="hidden" value="' . $title . '" class="title" />';
        $hiddenData .= '<input type="hidden" value="' . $row->getData('id') . '" class="rowId" />';
        $hiddenData .= '<input type="hidden" value="' . $trial . '" class="licenseType" />';
        return '
        <select  name="action" class="amconnectoraction" style="width:100px;">
            <option value="">Please Select</option>
            <option value="1" >Sync Now</option>
            <option value="2" >Sync Status</option>
        </select>' . $hiddenData;
    }
}
