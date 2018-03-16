<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Orderstatus\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $data = [
            ['Open','0'],
            ['Hold','0'],
            ['PendingApproval','0'],
            ['Voided','0'],
            ['CreditHold','0'],
            ['Completed','0'],
            ['Cancelled','0'],
            ['BackOrder','0'],
            ['Shipping','0'],
            ['Invoiced','0']
        ];
        $i = 0;
        foreach ($data as $row) {
            $i++;
            $bind = ['orderstatus_id'=> $i, 'status_label' => $row[0], 'status' => $row[1],'created_time'=> date('Y-m-d H:i:s')];
            $setup->getConnection()->insert($setup->getTable('amconnector_order_status'), $bind);
        }
    }
}
