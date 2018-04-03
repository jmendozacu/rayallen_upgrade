<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Synclog\Setup;


use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            /**
             * Create table 'amconnector_productprice_log'
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('amconnector_productprice_log'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )
                ->addColumn(
                    'sync_exec_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'sync_exec_id'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false],
                    'created_at'
                )
                ->addColumn(
                    'acumatica_attribute_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'acumatica_attribute_code'
                )
                ->addColumn(
                    'description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    2000,
                    ['nullable' => false],
                    'description'
                )
                ->addColumn(
                    'message_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'message_type'
                )
                ->addColumn(
                    'sync_direction',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'sync_direction'
                );
            $installer->getConnection()->createTable($table);


            $table = $installer->getConnection()
                ->newTable($installer->getTable('amconnector_productprice_log_details'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )
                ->addColumn(
                    'sync_record_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'sync_record_id'
                )
                ->addColumn(
                    'long_message',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    2000,
                    ['nullable' => false],
                    'long_message'
                );
            $installer->getConnection()->createTable($table);

            /**
             * Create table 'amconnector_productconfigurator_log'
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('amconnector_productconfigurator_log'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )
                ->addColumn(
                    'sync_exec_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'sync_exec_id'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false],
                    'created_at'
                )
 		->addColumn(
                   'product_id',
                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                   10,
                   ['nullable' => false],
                   'status'
       	      	)
                ->addColumn(
                    'acumatica_stock_item',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'acumatica_attribute_code'
                )
                ->addColumn(
                    'description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    2000,
                    ['nullable' => false],
                    'description'
                )
                ->addColumn(
                    'action',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    2000,
                    ['nullable' => false],
                    'action'
                )
                ->addColumn(
                    'sync_action',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    2000,
                    ['nullable' => false],
                    'sync_action'
                )
                ->addColumn(
                    'before_change',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    2000,
                    ['nullable' => false],
                    'run_mode'
                )
                ->addColumn(
                    'after_change',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    2000,
                    ['nullable' => false],
                    'auto_sync'
                )
                ->addColumn(
                    'message_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'message_type'
                );
            $installer->getConnection()->createTable($table);
            /**
             * Create table 'amconnector_productconfigurator_log_details'
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('amconnector_productconfigurator_log_details'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )
                ->addColumn(
                    'sync_record_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'schedule_id'
                )
                ->addColumn(
                    'long_message',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    2000,
                    ['nullable' => false],
                    'job_code'
                );
            $installer->getConnection()->createTable($table);


            /**
             * Create table 'amconnector_productimage_log'
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('amconnector_productimage_log'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )
                ->addColumn(
                    'sync_exec_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'sync_exec_id'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false],
                    'created_at'
                )
                ->addColumn(
                    'acumatica_attribute_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'acumatica_attribute_code'
                )
                ->addColumn(
                    'description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    2000,
                    ['nullable' => false],
                    'description'
                )
                ->addColumn(
                    'message_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'message_type'
                )
                ->addColumn(
                    'sync_direction',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'sync_direction'
                );
            $installer->getConnection()->createTable($table);


            $table = $installer->getConnection()
                ->newTable($installer->getTable('amconnector_productimage_log_details'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )
                ->addColumn(
                    'sync_record_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'sync_record_id'
                )
                ->addColumn(
                    'long_message',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    2000,
                    ['nullable' => false],
                    'long_message'
                );
            $installer->getConnection()->createTable($table);

            $installer->endSetup();
        }

    }
}
