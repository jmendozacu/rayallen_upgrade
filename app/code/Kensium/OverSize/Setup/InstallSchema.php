<?php
/**
 * Kensium_OverSize extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_OverSize
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\OverSize\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * install tables
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('kensium_oversize_oversizeship')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('kensium_oversize_oversizeship')
            )
            ->addColumn(
                'oversizeship_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'Over Size Ship ID'
            )
            ->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'Over Size Ship Sku'
            )
            ->addColumn(
                'price',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable => false'],
                'Over Size Ship Price'
            )

            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Over Size Ship Created At'
            )
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Over Size Ship Updated At'
            )
            ->setComment('Over Size Ship Table');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $installer->getTable('kensium_oversize_oversizeship'),
                $setup->getIdxName(
                    $installer->getTable('kensium_oversize_oversizeship'),
                    ['sku'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['sku'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        $installer->endSetup();
    }
}
