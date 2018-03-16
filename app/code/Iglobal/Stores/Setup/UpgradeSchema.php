<?php
namespace Iglobal\Stores\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface{
  public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context){
      // setup installer
      $installer = $setup;
      $installer->startSetup();

      // Update tables
      if(!$installer->getConnection()->tableColumnExists("sales_order", "international_order"))
      {
        $installer->getConnection()
            ->addColumn($installer->getTable('sales_order'),
                'international_order', "BOOLEAN NOT NULL DEFAULT 0"
        );
      }

      if(!$installer->getConnection()->tableColumnExists("sales_order", "ig_order_number"))
      {
        $installer->getConnection()
            ->addColumn($installer->getTable('sales_order'),
                'ig_order_number',  "VARCHAR( 15 ) NULL DEFAULT NULL , ADD INDEX ( `ig_order_number` )"
        );
      }

      if(!$installer->getConnection()->tableColumnExists("sales_order", "iglobal_test_order"))
      {
        $installer->getConnection()
            ->addColumn($installer->getTable('sales_order'),
                'iglobal_test_order',  "BOOLEAN NOT NULL DEFAULT 0"
        );
      }
      //finish
      $installer->endSetup();
  }
}
