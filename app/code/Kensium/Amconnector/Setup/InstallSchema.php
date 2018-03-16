<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */


namespace Kensium\Amconnector\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'amconnector_attribute_sync'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_attribute_sync'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Code'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true],
                'Store Id'
            )->addColumn(
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true],
                'Attribute Id'
            )
            ->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Title'
            )
            ->addColumn(
                'sync_enable',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Sync Enable'
            )
            ->addColumn(
                'last_sync_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [],
                'Last Sync Date'
            )
            ->addColumn(
                'sync_auto_cron',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Sync Auto Cron'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Status'
            )
            ->setComment('Amconnector Attribute Sync');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_attribute_mapping'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_attribute_mapping'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'ID'
            )
            ->addColumn(
                'magento_attr_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                150,
                [],
                'Magento Attribute Id'
            )
            ->addColumn(
                'acumatica_attr_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                '64k',
                [],
                'Acumatica Attribute Id'
            )
            ->addColumn(
                'sync_direction',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Sync Direction'
            )
            ->addColumn(
                'entity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Entity'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                200,
                [],
                'Status'
            )
            ->setComment('Amconnector Attribute Mapping');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_acumatica_category_attributes'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_acumatica_category_attributes'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Code'
            )
            ->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Label'
            )
            ->addColumn(
                'field_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Field Type'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector acumatica category attributes');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_category_mapping'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_category_mapping'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'magento_attr_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Attribute Code'
            )->addColumn(
                'acumatica_attr_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Acumatica Attribute Code'
            )->addColumn(
                'sync_direction',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Sync Direction'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector category mapping');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_acumatica_product_attributes'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_acumatica_product_attributes'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Code'
            )
            ->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Label'
            )
            ->addColumn(
                'field_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Field Type'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector acumatica product attributes');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_product_mapping'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_product_mapping'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'magento_attr_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Attribute Code'
            )->addColumn(
                'acumatica_attr_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Acumatica Attribute Code'
            )->addColumn(
                'sync_direction',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Sync Direction'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector product mapping');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_customer_mapping'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_customer_mapping'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'magento_attr_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Attribute Code'
            )->addColumn(
                'acumatica_attr_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Acumatica Attribute Code'
            )->addColumn(
                'sync_direction',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Sync Direction'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )->addColumn(
                'magento_attribute_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Attribute Id'
            )
            ->setComment('amconnector product mapping');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_acumatica_customer_attributes'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_acumatica_customer_attributes'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Code'
            )
            ->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Label'
            )
            ->addColumn(
                'field_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Field Type'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector acumatica customer attributes');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_acumatica_order_attributes'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_acumatica_order_attributes'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Code'
            )
            ->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Label'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector acumatica order attributes');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_order_mapping'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_order_mapping'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'magento_attr_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Attribute Code'
            )->addColumn(
                'acumatica_attr_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Acumatica Attribute Code'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector order mapping');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_syncstatus'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_syncstatus'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'sync_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Sync  Id'
            )->addColumn(
                'job_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Job Code'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Status'
            )->addColumn(
                'messages',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Messages'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => NULL],
                'Created At'
            )->addColumn(
                'scheduled_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => NULL],
                'Scheduled At'
            )->addColumn(
                'executed_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => NULL],
                'Executed At'
            )->addColumn(
                'finished_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['default' => NULL],
                'Finished At'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true],
                'Store Id'
            )->addColumn(
                'flag',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 1],
                'Flag'
            )
            ->setComment('Amconnector Sync Sync');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_category_sync_temp'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'magento_category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Magento Category Id'
            )->addColumn(
                'acumatica_category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Acumatica Category Id'
            )->addColumn(
                'acumatica_category_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Acumatica Category Description'
            )->addColumn(
                'acumatica_parent_category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Acumatica Parent Category Id'
            )->addColumn(
                'acumatica_parent_category_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Acumatica Parent Category Name'
            )->addColumn(
                'acumatica_category_path',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Acumatica Parent Category Path'
            )->addColumn(
                'acumatica_category_skus',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Acumatica Category Skus'
            )->addColumn(
                'acumatica_lastsyncdate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                255,
                [],
                'Acumatica Last Sync Date'
            )->addColumn(
                'magento_lastsyncdate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                255,
                [],
                'Magento Last Sync Date'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                11,
                [],
                'Store Id'
            )->addColumn(
                'flg',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Flag'
            )->addColumn(
                'entity_ref',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                [],
                'Entity Reference'
            )->addColumn(
                'entity_ref',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                [],
                'Entity Reference'
            )
            ->setComment('amconnector category sync temp');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_server_timing'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_server_timing'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'ID'
            )
            ->addColumn(
                'magento_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Magento Time'
            )->addColumn(
                'magento_timezone',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Magento TimeZone'
            )->addColumn(
                'accumatica_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Acumatica Time'
            )->addColumn(
                'accumatica_timezone',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Acumatica TimeZone'
            )->addColumn(
                'verified_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Verified At'
            )->addColumn(
                'verified_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Verified Status'
            )->addColumn(
                'scope_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Scope Id'
            )
            ->setComment('Amconnector Server Timing');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_customer_sync_temp'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_customer_sync_temp'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Email'
            )->addColumn(
                'acumatica_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'default' => null],
                'Acumatica Id'
            )->addColumn(
                'magento_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'default' => null],
                'Magento Id'
            )->addColumn(
                'magento_lastsyncdate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'default' => null],
                'Magento Last Sync Date'
            )->addColumn(
                'acumatica_lastsyncdate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'default' => null],
                'Acumatica Last Sync Date'
            )->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'default' => null],
                'Website Id'
            )->addColumn(
                'entity_ref',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'default' => null],
                'Entity Ref'
            )->addColumn(
                'flg',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Flag'
            )
            ->setComment('amconnector customer sync temp');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'amconnector_customer_deleted_data'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_customer_deleted_data'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Email'
            )->addColumn(
                'acumatica_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Acumatica Id'
            )->addColumn(
                'magento_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Id'
            )->addColumn(
                'deleted_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => false],
                'deleted_date'
            )
            ->setComment('amconnector customer deleted data');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_customer_attribute_mapping'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_customer_attribute_mapping'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )->addColumn(
                'entity_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Entity Type'
            )
            ->addColumn(
                'magento_attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Attribute Code'
            )->addColumn(
                'acumatica_attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Acumatica Attribute Code'
            )->addColumn(
                'field_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Field Type'
            )->addColumn(
                'is_common',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Sync Direction'
            )->addColumn(
                'is_required',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'is required'
            )->addColumn(
                'is_unique',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'is unique'
            )->addColumn(
                'field_values',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Field values'
            )->addColumn(
                'magento_field_values',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Field values'
            )->addColumn(
                'flag',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'flag'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector customer attribute mapping');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_ship_mapping'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_ship_mapping'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'magento_attr_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Attribute Code'
            )->addColumn(
                'carrier',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Attribute Code'
            )->addColumn(
                'acumatica_attr_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Acumatica Attribute Code'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector ship mapping');
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'amconnector_acumatica_ship_attributes'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_acumatica_ship_attributes'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Code'
            )
            ->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Label'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector acumatica ship attributes');
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'amconnector_payment_mapping'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_payment_mapping'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'magento_attr_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Attribute Code'
            )->addColumn(
                'acumatica_attr_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Acumatica Attribute Code'
            )->addColumn(
                'cash_account',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'cash account'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector payment mapping');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_acumatica_payment_attributes'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_acumatica_payment_attributes'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Code'
            )
            ->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Label'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector acumatica payment attributes');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_acumatica_payment_attributes'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_acumatica_cashaccount_attribute'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'cash_account',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Cash Account'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('amconnector_acumatica_cashaccount_attribute');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_customer_order_mapping'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_customer_order_mapping'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Email'
            )
            ->addColumn(
                'acumatica_customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Acumatica Customer Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Store Id'
            )
            ->addColumn(
                'updated_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Updated Date'
            )
            ->setComment('amconnector_customer_order_mapping');
        $installer->getConnection()->createTable($table);


        /**
         * Alter sales_order table add acumatica_order_id,sync_order_failed fields
         */
        $salesTable = $installer->getTable('sales_order');

        $columns = [
            'acumatica_order_id' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                'nullable' => false,
                'comment' => 'Saves Acumatica OrderId',
            ],
            'sync_order_failed' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => '1',
                'nullable' => false,
                'comment' => 'Saves Sync failed status',
            ],
            'failed_retry_count' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => '1',
                'nullable' => false,
                'comment' => 'failed retry count',
            ],
        ];
        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($salesTable, $name, $definition);
        }

        /**
         * Create table 'amconnector_license_check'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_license_check'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'license_key',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'License Key'
            )
            ->addColumn(
                'license_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'License Status'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->addColumn(
                'license_url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'License Url'
            )
            ->addColumn(
                'request_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Request Data'
            )
            ->addColumn(
                'verified_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Verified Date'
            )
            ->setComment('Amconnector License');

        $installer->getConnection()->createTable($table);


        $installer->endSetup();
    }
}
