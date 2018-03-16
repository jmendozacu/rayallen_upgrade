<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $taxCategorySync = ['id'=> '','code' => 'taxCategory' , 'store_id' => '1', 'attribute_id' => '0', 'title' => 'Tax Category Sync','sync_enable'=> '1','last_sync_date'=> NULL,'sync_auto_cron'=> '0','status'=> ''];
        $setup->getConnection()->insert($setup->getTable('amconnector_attribute_sync'), $taxCategorySync);

    }
}
