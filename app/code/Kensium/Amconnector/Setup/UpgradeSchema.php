<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Setup;


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
         * Create table 'amconnector_warehouse_details'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_warehouse_details'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )->addColumn(
                'warehouse_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Warehouse Name'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true],
                'Store Id'
            )
            ->setComment('Amconnector Warehouse Details');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_customerclass_details'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_customerclass_details'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true],
                'Id'
            )->addColumn(
                'customer_class',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Class'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true],
                'Store Id'
            )
            ->setComment('Amconnector Customer Class Details');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_customerterms_details'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_customerterms_details'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true],
                'Id'
            )->addColumn(
                'customer_term',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Terms'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true],
                'Store Id'
            )
            ->setComment('Amconnector Customer Terms Details');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_customercycle_details'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_customercycle_details'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true],
                'Id'
            )->addColumn(
                'customer_cycle',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Cycle'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true],
                'Store Id'
            )
            ->setComment('Amconnector Customer Cycle Details');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_salesaccount_details'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_salesaccount_details'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true],
                'Id'
            )->addColumn(
                'sales_account',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Sales Account'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true],
                'Store Id'
            )
            ->setComment('Amconnector Sales account details');
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
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true],
                'Id'
            )->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Code'
            )
            ->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Label'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('Amconnector Acumatica Payment Method details');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_sync_direction_data'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_sync_direction_data'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )->addColumn(
                'path',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Path'
            )->addColumn(
                'value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Value'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Store Id'
            )
            ->setComment('Amconnector Sync Direction Details');
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
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true],
                'Id'
            )->addColumn(
                'cash_account',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Cash Account'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('Amconnector Cash Account details');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_product_sync_temp'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_product_sync_temp'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true],
                'Id'
            )->addColumn(
                'acumatica_inventory_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Acumatica inventory Id'
            )->addColumn(
                'magento_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Magento Sku'
            )->addColumn(
                'magento_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Magento Id'
            )->addColumn(
                'magento_lastsyncdate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Magento Last Sync Date'
            )->addColumn(
                'acumatica_lastsyncdate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Acumatica Last Sync Date'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )->addColumn(
                'entity_ref',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Entity Reference'
            )->addColumn(
                'flg',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Flag'
            );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_group_product_sync_temp'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_group_product_sync_temp'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true],
                'Id'
            )->addColumn(
                'acumatica_inventory_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Acumatica inventory Id'
            )->addColumn(
                'magento_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Magento Sku'
            )->addColumn(
                'magento_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Magento Id'
            )->addColumn(
                'magento_lastsyncdate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Magento Last Sync Date'
            )->addColumn(
                'acumatica_lastsyncdate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Acumatica Last Sync Date'
            )->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website Id'
            )->addColumn(
                'entity_ref',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Entity Reference'
            )->addColumn(
                'flg',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Flag'
            );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_group_product_sync_temp'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_bundle_product_sync_temp'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true],
                'Id'
            )->addColumn(
                'acumatica_inventory_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Acumatica inventory Id'
            )->addColumn(
                'magento_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Magento Sku'
            )->addColumn(
                'magento_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Magento Id'
            )->addColumn(
                'magento_lastsyncdate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Magento Last Sync Date'
            )->addColumn(
                'acumatica_lastsyncdate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Acumatica Last Sync Date'
            )->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website Id'
            )->addColumn(
                'entity_ref',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Entity Reference'
            )->addColumn(
                'flg',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Flag'
            );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_configurable_product_sync_temp'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_configurable_product_sync_temp'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true],
                'Id'
            )->addColumn(
                'acumatica_inventory_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Acumatica inventory Id'
            )->addColumn(
                'magento_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Magento Sku'
            )->addColumn(
                'magento_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Magento Id'
            )->addColumn(
                'magento_lastsyncdate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Magento Last Sync Date'
            )->addColumn(
                'acumatica_lastsyncdate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Acumatica Last Sync Date'
            )->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website Id'
            )->addColumn(
                'entity_ref',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Entity Reference'
            )->addColumn(
                'flg',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Flag'
            );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_product_attribute_mapping'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_product_attribute_mapping'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )
            ->addColumn(
                'entity_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                55,
                ['unsigned' => true, 'nullable' => false],
                'Entity Type'
            )
            ->addColumn(
                'acumatica_attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['unsigned' => true, 'nullable' => true],
                'Acumatica Attribute Code'
            )
            ->addColumn(
                'magento_attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['unsigned' => true, 'nullable' => false],
                'Magento Attribute Code'
            )
            ->addColumn(
                'is_common',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Is Common'
            )
            ->addColumn(
                'field_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['unsigned' => true, 'nullable' => false],
                'Field Type'
            )
            ->addColumn(
                'is_required',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                55,
                ['unsigned' => true, 'nullable' => false],
                'Is Required'
            )
            ->addColumn(
                'is_unique',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                55,
                ['unsigned' => true, 'nullable' => false],
                'Is Unique'
            )
            ->addColumn(
                'field_values',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Field Values'
            )
            ->addColumn(
                'magento_field_values',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Magento Feild Values'
            )
            ->addColumn(
                'flag',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                55,
                ['unsigned' => true, 'nullable' => false],
                'Flag'
            )
            ->setComment('Kems License');

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amconnector_custom_product_attributes'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amconnector_custom_product_attributes'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Id'
            )->addColumn(
                'attributeid',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Attribute Id'
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Description'
            )->addColumn(
                'controlltype',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Controll Type'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->setComment('Amconnector Custome Product Attributes');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
