<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
     
namespace Kensium\Orderstatus\Setup;
     
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
     
class InstallSchema implements InstallSchemaInterface
    {
     public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
        {
            $installer = $setup;
            $installer->startSetup();
            // Get testimonial table
            $tableName = $installer->getTable('amconnector_order_status');
            // Check if the table already exists
            if ($installer->getConnection()->isTableExists($tableName) != true) {
                // Create testimonial table
                $table = $installer->getConnection()
                    ->newTable($tableName)
                    ->addColumn(
                        'orderstatus_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'unsigned' => true,
                            'nullable' => false,
                            'primary' => true
                        ],
                        'Orderstatus_Id'
                    )
                    ->addColumn(
                        'status_label',
                        Table::TYPE_TEXT,
                        null,
                        ['nullable' => false, 'default' => ''],
                        'Status_Label'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false,'unsigned' => true, ],
                        'Status'
                    )
                    ->addColumn(
                        'created_time',
                        Table::TYPE_DATETIME,
                        null,
                        ['nullable' => false],
                        'Created_Time'
                    )
                    ->setComment('Orderstatus')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($table);
            }
            $installer->endSetup();
        }
    }
