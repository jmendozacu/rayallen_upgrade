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
 * Shipment resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ship extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('amconnector_ship_mapping', 'id');
    }

    /**
     * @param array $data
     * @param $storeId
     */
    public function updateShipSchema($data = array(),$storeId)
    {
	$storeId = 1;
        try{
            $this->getConnection()->query("DELETE FROM " . $this->getTable("amconnector_acumatica_ship_attributes")." WHERE store_id=".$storeId);
        }catch (Exception $e){
            echo $e->getMessage();
        }
        if(count($data) > 0){
            foreach ($data->Entity as $dataValue) {
                $finalValue = $dataValue->CarrierID->Value;
                $finalDescription = $dataValue->Description->Value;
                $this->getConnection()->query("INSERT INTO " . $this->getTable("amconnector_acumatica_ship_attributes") . " set label='" . $finalDescription . "',code='" . $finalValue . "',store_id = ".$storeId);
            }
        }
    }

    /**
     * @param $storeId
     * @return string
     */
    public function getAcumaticaAttrCount($storeId)
    {
        try{
            $acumaticaCount = $this->getConnection()->fetchOne("SELECT count(*) FROM " . $this->getTable('amconnector_acumatica_ship_attributes')." WHERE store_id=".$storeId);
        }catch (Exception $e){
            echo $e->getMessage();
        }
        return $acumaticaCount;
    }
}
