<?php
/**
* Create Order Attributes for Storing Acumatica Order Id Details
*/
namespace Kensium\OrderAttribute\Setup;

use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\ResourceConnection;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }


    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->resource->getConnection('sales_order')->addColumn(
            $setup->getTable('sales_order_grid'),
            'acumatica_order_id',
            [
                'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'COMMENT' => 'acumatica order id'
            ]
        );

    }
}
