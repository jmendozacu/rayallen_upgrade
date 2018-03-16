<?php

namespace Kensium\GiftCard\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context){
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {

            // Get module table
            $tableName = $setup->getTable('sales_order');
            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'acm_order_number' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'LENGTH' => 100,
                        'comment' => 'Acumatica Order Reference',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        $setup->endSetup();
    }
}
