<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\Review\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Product resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Review extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var Manager
     */
    protected $cacheManager;

    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param Manager $cacheManager
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        Manager $cacheManager,
        $connectionName = null
    )
    {
        $this->cacheManager = $cacheManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        // $this->_init('kems_product_mapping', 'id');
    }


    public function getProductIdBySku($sku)
    {
        //$sku=trim($sku);
        $query = "SELECT entity_id  FROM " . $this->getTable('catalog_product_entity') . " WHERE sku = '" . $sku . "'";
        $entityId = $this->getConnection()->fetchOne($query);
        return $entityId;
    }
}