<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Payment resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Payment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
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
        $this->_init('amconnector_payment_mapping', 'id');
    }



    /**
     * @param array $data
     */
    public function updatePaymentSchema($data = array(),$storeId)
    {
	$storeId = 1;
        $this->getConnection()->query("DELETE FROM " . $this->getTable("amconnector_acumatica_payment_attributes")." WHERE store_id =". $storeId);
        $this->getConnection()->query("DELETE FROM " . $this->getTable("amconnector_acumatica_cashaccount_attribute")." WHERE store_id =". $storeId);
        foreach ($data->Entity as $dataValue) {
            $finalValue = $dataValue->PaymentMethodID->Value;
            $active = $dataValue->Active->Value;
            if(strtolower($active) == 'true')
            {
                $this->getConnection()->query("INSERT INTO " . $this->getTable("amconnector_acumatica_payment_attributes") . " set label='" . $finalValue . "',code='" . $finalValue . "',store_id='" . $storeId . "' ");
            }else if (strtolower($active) == 'false' )
            {
                $paymentCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_payment_mapping')." WHERE acumatica_attr_code = '".$finalValue."' AND store_id=".$storeId);
                if($paymentCount)
                {
                    $this->getConnection()->query("DELETE FROM " . $this->getTable("amconnector_payment_mapping")." WHERE acumatica_attr_code = '".$finalValue."' AND store_id=".$storeId);
                }
            }
        }
        $cashAccountArray = array();
        foreach($data->Entity as $dataValue) {
            $active = $dataValue->Active->Value;
            if($dataValue->CashAccounts->CashAccounts){
                foreach($dataValue->CashAccounts->CashAccounts as $cashData){
                    $value = trim($cashData->CashAccount->Value);
                    if(!in_array($value,$cashAccountArray) && strtolower($active) == 'true'){
                        $cashAccountArray[] = $value;
                        $this->getConnection()->query("INSERT INTO " . $this->getTable("amconnector_acumatica_cashaccount_attribute")." VALUES ('','".$value."','".$storeId."' )  ");
                    }
                }
            }
        }

    }

    /**
     * @return mixed
     */
    public function getCashAccountAttributes($storeId)
    {
	$storeId = 1;
        $results = $this->getConnection()->fetchAll("SELECT * FROM " . $this->getTable("amconnector_acumatica_cashaccount_attribute")." where store_id=".$storeId);
        return $results;
    }

    /**
     * @param $storeId
     * @return string
     */
    public function getAcumaticaAttrCount($storeId)
    {
        try{
            $acumaticaCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_acumatica_payment_attributes')." WHERE store_id=".$storeId);
        }catch (Exception $e){
            echo $e->getMessage();
        }
        return $acumaticaCount;
    }

}
