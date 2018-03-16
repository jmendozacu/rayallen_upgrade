<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Synclog\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_category_log'))
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
                'schedule_id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'job_code'
            )
            ->addColumn(
                'cat_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'status'
            )
            ->addColumn(
                'acumatica_category_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'messages'
            )
            ->addColumn(
                'acumatica_category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'created_at'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'scheduled_at'
            )
            ->addColumn(
                'action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'executed_at'
            )
            ->addColumn(
                'sync_action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'finished_at'
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
                'store_id'
            );
        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_customerattribute_log'))
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
                'schedule_id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'job_code'
            )
            ->addColumn(
                'acumatica_attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'status'
            )
            ->addColumn(
                'magento_attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'messages'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'created_at'
            )
            ->addColumn(
                'message_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'scheduled_at'
            )
            ->addColumn(
                'sync_direction',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'executed_at'
            );
        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_customer_log'))
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
                'schedule_id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'job_code'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'status'
            )->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'email'
            )
            ->addColumn(
                'accumatica_customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'messages'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'created_at'
            )
            ->addColumn(
                'action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'scheduled_at'
            )
            ->addColumn(
                'sync_action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'executed_at'
            )
            ->addColumn(
                'sync_direction',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'finished_at'
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
                'store_id'
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_shipment_log'))
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
                'schedule_id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'job_code'
            )
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'status'
            )
            ->addColumn(
                'acumatica_order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'messages'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'created_at'
            )
            ->addColumn(
                'action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'scheduled_at'
            )
            ->addColumn(
                'sync_action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'executed_at'
            )
            ->addColumn(
                'message_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'store_id'
            )
            ->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'store_id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'store_id'
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_order_log'))
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
                'schedule_id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'job_code'
            )
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'status'
            )
            ->addColumn(
                'acumatica_order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'messages'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'created_at'
            )
            ->addColumn(
                'action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'scheduled_at'
            )
            ->addColumn(
                'sync_action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'executed_at'
            )
            ->addColumn(
                'message_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'store_id'
            )
            ->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'store_id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'store_id'
            );
        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_productattribute_log'))
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
                'schedule_id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'job_code'
            )
            ->addColumn(
                'acumatica_attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'status'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'created_at'
            )
            ->addColumn(
                'message_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'store_id'
            )
            ->addColumn(
                'sync_direction',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'store_id'
            );
        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_productinventory_log'))
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
                'schedule_id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'job_code'
            )
            ->addColumn(
                'acumatica_attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'status'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'created_at'
            )
            ->addColumn(
                'message_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'store_id'
            )
            ->addColumn(
                'sync_direction',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'store_id'
            );
        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_product_log'))
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
                'schedule_id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'job_code'
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
                255,
                ['nullable' => false],
                'messages'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'created_at'
            )
            ->addColumn(
                'action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'scheduled_at'
            )
            ->addColumn(
                'sync_action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'executed_at'
            )
            ->addColumn(
                'before_change',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'store_id'
            )
            ->addColumn(
                'after_change',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'store_id'
            )
            ->addColumn(
                'message_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                225,
                ['nullable' => false],
                'store_id'
            );
        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_merchandise_log'))
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
                'schedule_id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'job_code'
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
                255,
                ['nullable' => false],
                'messages'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'created_at'
            )
            ->addColumn(
                'action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'scheduled_at'
            )
            ->addColumn(
                'sync_action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'executed_at'
            )
            ->addColumn(
                'before_change',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'store_id'
            )
            ->addColumn(
                'after_change',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'store_id'
            )
            ->addColumn(
                'message_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                225,
                ['nullable' => false],
                'store_id'
            );
        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_taxcategory_log'))
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
                'schedule_id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'job_code'
            )
            ->addColumn(
                'acumatica_attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'messages'
            )
            ->addColumn(
                'magento_attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'created_at'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'scheduled_at'
            )
            ->addColumn(
                'message_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'executed_at'
            )
            ->addColumn(
                'sync_direction',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'store_id'
            );
        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_category_log_details'))
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


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_customerattribute_log_details'))
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


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_customer_log_details'))
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


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_order_log_details'))
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

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_shipment_log_details'))
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

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_productattribute_log_details'))
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


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_productinventory_log_details'))
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


        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_product_log_details'))
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

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_merchandise_log_details'))
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

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_taxcategory_log_details'))
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


        $installer->endSetup();
    }
}
