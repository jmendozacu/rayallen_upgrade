<?php

/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Csvenvelopes\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Install schema
 * @category Kensium
 * @package  Kensium_Csvenvelopes
 * @module   Csvenvelopes
 * @author   Kensium Developer
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /*
         * Drop tables if exists
         */
        $installer->getConnection()->dropTable($installer->getTable('amconnector_csvenvelopes'));

        /*
         * Create table kensium_bannerslider_slider
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('amconnector_csvenvelopes')
        )->addColumn(
            'csvenvelopes_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Envelopes Id'
        )->addColumn(
            'envcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['nullable' => false, 'default' => ''],
            'Envelop Code'
        )->addColumn(
            'enventity',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            25,
            ['nullable' => false, 'default' => ''],
            'Envelop Entity'
        )->addColumn(
            'envtype',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['nullable' => false, 'default' => ''],
            'Env Type'
        )->addColumn(
            'envversion',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            25,
            ['nullable' => false, 'default' => ''],
            'Envelop Version'
        )->addColumn(
            'envname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            25,
            ['nullable' => false, 'default' => ''],
            'Envelop Name'
        )->addColumn(
            'methodname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            25,
            ['nullable' => false, 'default' => ''],
            'Method Name'
        )->addColumn(
            'envelope',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10000,
            ['nullable' => false, 'default' => ''],
            'Envelop'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'Updated At'
        )->addColumn(
            'acumaticaversion',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            ['nullable' => true,'default' =>''],
            'Acumatica Version'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}