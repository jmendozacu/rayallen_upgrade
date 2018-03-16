<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Scheduler\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_sync_cron_schedule'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'schedule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'schedule_id'
            )
            ->addColumn(
                'job_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'job_code'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                7,
                ['nullable' => false],
                'status'
            )
            ->addColumn(
                'messages',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'messages'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => NULL],
                'created_at'
            )
            ->addColumn(
                'scheduled_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => NULL],
                'scheduled_at'
            )
            ->addColumn(
                'executed_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => NULL],
                'executed_at'
            )
            ->addColumn(
                'finished_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => NULL],
                'finished_at'
            )
            ->addColumn(
                'run_mode',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'run_mode'
            )
            ->addColumn(
                'auto_sync',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2000,
                ['nullable' => false],
                'auto_sync'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'store_id'
            );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
