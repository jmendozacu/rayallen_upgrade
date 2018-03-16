<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Order extends AbstractDb
{
    /**
     * @param Context $context
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
    }

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amconnector_order_mapping', 'id');
    }

    public function getAcumaticaAttrCount()
    {
        $acumaticaCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_acumatica_order_attributes'));
        return $acumaticaCount;
    }

    /**
     *Checking count of the mapping table
     */
    public function checkOrderMapping($storeId)
    {
        $orderAttributes = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_order_mapping') . " WHERE store_id =" . $storeId);
        return $orderAttributes;
    }

    /**
     * Get Acumatica payment method and CashAccount
     * @param $magentoPaymentMethod
     * @param $storeId
     * @return array
     */
    public function getMappingPaymentMethod($magentoPaymentMethod,$storeId){
        $paymentMethod =  array();
        $acumaticaPaymentMethod = $this->getConnection()->fetchAll("SELECT `acumatica_attr_code`,`cash_account` FROM " . $this->getTable('amconnector_payment_mapping') . " WHERE `magento_attr_code`='" . $magentoPaymentMethod . "'  and store_id=".$storeId);
        if(isset( $acumaticaPaymentMethod[0]['acumatica_attr_code']))
            $paymentMethod['paymentmethod'] = $acumaticaPaymentMethod[0]['acumatica_attr_code'];
        if(isset($acumaticaPaymentMethod[0]['cash_account']))
            $paymentMethod['cashaccount'] = $acumaticaPaymentMethod[0]['cash_account'];
        return $paymentMethod;
    }

    /**
     * Get Acumatica shipping method
     * @param $magentoshippingMethod
     * @param $storeId
     * @return String
     */
    public function getMappingShipmentMethod($magentoshippingMethod,$storeId){
        $acumaticaShipmentMethod = $this->getConnection()->fetchOne("SELECT `acumatica_attr_code` FROM " . $this->getTable('amconnector_ship_mapping') . " WHERE `magento_attr_code`='" . $magentoshippingMethod . "'  and store_id=".$storeId);
        return $acumaticaShipmentMethod;
    }

    /**
     * To get Statecode By Id
     * @param $id
     * @return string
     */
    public function getStateCodeById($id)
    {
        $stateCode = $this->getConnection()->fetchOne("SELECT code  from  " .$this->getTable('directory_country_region')." where region_id='" . $id . "'");
        return $stateCode;
    }

    /**
     *
     * @param $entityId
     * @return string
     */

    public function getCustomerId($entityId)
    {
        $attributeId = $this->getConnection()->fetchOne("SELECT attribute_id FROM " . $this->getTable('eav_attribute') . " WHERE `attribute_code` = 'acumatica_customer_id' and  entity_type_id =1");
        $acumatiacCustomerId = $this->getConnection()->fetchOne("SELECT value FROM " . $this->getTable('customer_entity_varchar') . " where attribute_id = ".$attributeId." and entity_id = ".$entityId);
        return   $acumatiacCustomerId;
    }

    /**
     *
     * @param $entityId
     * @return string
     */

    public function getGuestCustomerId($email,$storeId)
    {
        $email = addslashes($email);
        $acumatiacCustomerId = $this->getConnection()->fetchOne("SELECT acumatica_customer_id  FROM " . $this->getTable('amconnector_customer_order_mapping') . " WHERE `email` = '".$email."' and  store_id =".$storeId);

        return   $acumatiacCustomerId;
    }

    /**
     * @param array $syncData
     */
    public function updateMagentoOrderData($syncData = array())
    {
        foreach ($syncData as $dataValue) {
            $orderID = $this->getConnection()->fetchOne("select magento_order_id from " . $this->getTable("amconnector_acumatica_orders_temp")." where magento_order_id='" . $dataValue['increment_id'] . "'");
            if (!$orderID) {

                $insertQry = "INSERT INTO " . $this->getTable("amconnector_acumatica_orders_temp")." (acumatica_orderid,acumatica_order_status,acumatica_lastsyncdate,magento_order_id,magento_order_status,magento_lastsyncdate,flag)
          VALUES ('','','','" . $dataValue['increment_id'] . "','" . $dataValue['status'] . "','" . $dataValue['updated_at'] . "',1)";

                $this->getConnection()->query($insertQry);
            }


        }


    }

    /**
     * @param array $syncData
     */
    public function updateAcumaticaOrderData($acumaticaOrderNbr, $date, $magentoOrderNbr)
    {
        $updateQry = "UPDATE " . $this->getTable("amconnector_acumatica_orders_temp"). " SET acumatica_orderid ='" . $acumaticaOrderNbr . "', acumatica_lastsyncdate = '" . date('Y-m-d h:i:s', strtotime($date)) . "' WHERE magento_order_id = '" . $magentoOrderNbr . "'";
        $this->getConnection()->query($updateQry);
        $Qry = "UPDATE " . $this->getTable("sales_flat_order"). " SET acumatica_order_id ='" . $acumaticaOrderNbr . "' WHERE increment_id = '" . $magentoOrderNbr . "'";
        $this->getConnection()->query($Qry);

    }



    /**
     * Update Acumatica Order data in temp table
     *
     * @param $magentoOrderNbr
     * @param $acumaticaOrderStatus
     * @param $magentoOrderStatus
     */
    public function updateOrderStatus($magentoOrderNbr, $acumaticaOrderStatus, $magentoOrderStatus)
    {
        $updateQry = "UPDATE " . $this->getTable("amconnector_acumatica_orders_temp")." SET acumatica_order_status ='" . $acumaticaOrderStatus . "', magento_order_status = '" . $magentoOrderStatus . "' WHERE magento_order_id = '" . $magentoOrderNbr . "'";
        $this->getConnection()->query($updateQry);

    }

    /**
     * @param $email
     * @return string
     */
    public function getCustomerIdByEmail($email)
    {
        $email = addslashes($email);
        $magentoCustomerId = $this->getConnection()->fetchOne("SELECT entity_id FROM " . $this->getTable('customer_entity') . " where email= '".$email."'");
        return   $magentoCustomerId;
    }

    /**
     * @return mixed
     * Get stop sync value for database
     */

    public function StopSyncValue()
    {
        $query = "select value from " . $this->getTable("core_config_data")." where path ='amconnectorsync/ordersync/syncstopflg' ";
        $value = $this->getConnection()->fetchOne($query);
        return $value;
    }

    public function enableSync()
    {
        $path = 'amconnectorsync/ordersync/syncstopflg';
        $query = "update " . $this->getTable("core_config_data")." set value = 1 where path ='" . $path . "'";
        $this->getConnection()->query($query);
    }


    /**
     * @param $acumaticaOrderNbr
     * @param $storeId
     */
    public function getMagentoOrderIdAcuOrderId($acumaticaOrderNbr, $storeId)
    {
        $magentoOrderNbr = $this->getConnection()->fetchOne("SELECT increment_id FROM " . $this->getTable('sales_order') . " WHERE acumatica_order_id='" . $acumaticaOrderNbr . "'");
        return $magentoOrderNbr;
    }

    /**
     * Get Magento shipping method
     * @param $acumaticashippingMethod
     * @param $storeId
     * @return String
     */
    public function getMagentoMappingShipmentMethod($acumaticashippingMethod,$storeId){
        $magentoShipmentMethodResult = $this->getConnection()->fetchAll("SELECT `magento_attr_code`,`carrier` FROM " . $this->getTable('amconnector_ship_mapping') . " WHERE `acumatica_attr_code`='" . $acumaticashippingMethod . "'  and store_id=".$storeId);
        $magentoShipmentMethod = array();
        if(isset($magentoShipmentMethodResult[0]['carrier']))
            $magentoShipmentMethod['carrier']=$magentoShipmentMethodResult[0]['carrier'];
        if(isset($magentoShipmentMethodResult[0]['magento_attr_code']))
            $magentoShipmentMethod['magento_attr_code']=$magentoShipmentMethodResult[0]['magento_attr_code'];
        return $magentoShipmentMethod;
    }


    /**
     * @param $acumaticaOrderNbr
     * @param $storeId
     */
    public function checkShipmentTrackNumber($trackingNumber)
    {
        $enitytId = $this->getConnection()->fetchOne("SELECT entity_id FROM " . $this->getTable('sales_shipment_track') . " WHERE track_number='" . $trackingNumber . "' ");
        return $enitytId;
    }

    /**
     * @param $orderId
     * @param $parentItemId
     */
    public function getParentItemPrice($orderId, $parentItemId)
    {
        $itemPrice = $this->getConnection()->fetchOne("SELECT price FROM " . $this->getTable('sales_order_item') . " WHERE order_id=".$orderId ." and item_id =".$parentItemId);
        return $itemPrice;
    }


    /**
     * @param $acumaticaOrderStatus
     * @param $storeId
     */
    public function getMagentoOrderStatusMapping($acumaticaOrderStatus,$storeId){
        echo $magentoOrderStatusCode = $this->getConnection()->fetchOne("SELECT `magento_attr_code` FROM " . $this->getTable('amconnector_order_mapping') . " WHERE `acumatica_attr_code`='" . $acumaticaOrderStatus . "'  and store_id=".$storeId);
        return $magentoOrderStatusCode;
    }

    /**
     * @param $magentoOrderStatusCode
     */
    public function getMagentoOrderStateCode($magentoOrderStatusCode){

        $magentoOrderStateCode = $this->getConnection()->fetchOne("SELECT `state` FROM " . $this->getTable('sales_order_status_state') . " WHERE `status`='" . $magentoOrderStatusCode . "' ");
        return $magentoOrderStateCode;
    }


    /**
     * @param $orderNumber
     */
    public function updateOrderStatusMapping($orderNumber,$magentoOrderStatusCode,$magentoOrderStateCode){

        $this->getConnection()->query("UPDATE ". $this->getTable('sales_order') . " set state='".$magentoOrderStateCode."',status='".$magentoOrderStatusCode."' where increment_id='".$orderNumber."'");
        $this->getConnection()->query("UPDATE ". $this->getTable('sales_order_grid') ." set status='".strtolower($magentoOrderStatusCode)."' where increment_id='".$orderNumber."'");
    }


    /**
     * @param $orderId
     * @param $parentItemId
     */
    public function getParentProductId($orderId, $parentItemId)
    {
        $productId = $this->getConnection()->fetchAll("SELECT product_id,price FROM " . $this->getTable('sales_order_item') . " WHERE order_id=".$orderId ." and item_id =".$parentItemId);
        return $productId;
    }

    /**
     * @param $storeId
     */
    public function getSyncIdforOrder($storeId)
    {
        $syncId = $this->getConnection()->fetchOne("SELECT id FROM " . $this->getTable('amconnector_attribute_sync') . " WHERE code ='order' and store_id =".$storeId);
        return $syncId;
    }


    /**
     * @param $orderId
     * @param $orderDiscountAmount
     */
    public function getOrderDiscountAmount($orderId,$orderDiscountAmount){

        $result  = $this->getConnection()->fetchOne("SELECT sum(discount_amount) FROM " . $this->getTable('sales_order_item') . " WHERE order_id= ".$orderId." ");

        $orderDiscountAmount = $orderDiscountAmount *-1;
        if($orderDiscountAmount !=$result)
            $discountAmount  =  $orderDiscountAmount-$result;
        else
            $discountAmount = 0;
        return $discountAmount;
    }


    /**
     * @param $orderId
     */
    public function getLastItemId($orderId){

        $itemId = $this->getConnection()->fetchOne("SELECT max(item_id) FROM " .  $this->getTable('sales_order_item') . " WHERE order_id =" . $orderId . " AND product_type='simple'");
        return $itemId;
    }

    /**
     * @param $parentItemId
     */
    public function getParentProductDiscount($parentItemId){

        $parentProductDiscount = $this->getConnection()->fetchOne("SELECT `discount_amount` FROM " .  $this->getTable('sales_order_item') . " WHERE item_id =" . $parentItemId . "");
        return $parentProductDiscount;
    }

    /**
     * @param $orderId
     * @param $parentItemId
     */
    public function getParentItemQunatity($orderId, $parentItemId)
    {
        $itemQty = $this->getConnection()->fetchOne("SELECT qty_ordered FROM " . $this->getTable('sales_order_item') . " WHERE order_id=".$orderId ." and item_id =".$parentItemId);
        return $itemQty;
    }
	
	
	/**
     * To get Id By Statecode
     * @param $code
     * @return int
     */
    public function getStateIdByCode($code)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('directory_country_region'),
            ['region_id']
        )->where(
            'code=?',
            $code
        );
        return $connection->fetchOne($select);
    }

    /**
     * @param $sku
     * @return int
     */
    public function getIdBySku($sku)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('catalog_product_entity'),
            ['entity_id']
        )->where(
            'sku=?',
            $sku
        );
        return $connection->fetchOne($select);
    }
}
