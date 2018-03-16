<?php
namespace Iglobal\Stores\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface{
  public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){
      // setup installer
      $installer = $setup;
      $installer->startSetup();

      // Update tables
      $installer->getConnection()
          ->addColumn($installer->getTable('sales_order'),
              'international_order', "BOOLEAN NOT NULL DEFAULT 0"
      );
      $installer->getConnection()
          ->addColumn($installer->getTable('sales_order'),
              'ig_order_number',  "VARCHAR( 15 ) NULL DEFAULT NULL , ADD INDEX ( `ig_order_number` )"
      );
      $installer->getConnection()
          ->addColumn($installer->getTable('sales_order'),
              'iglobal_test_order',  "BOOLEAN NOT NULL DEFAULT 0"
      );

      //finish
      $installer->endSetup();
  }
}
