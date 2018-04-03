<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */


namespace Kensium\Amconnector\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $attributeSetFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /*
    * @var int
    */
    protected $entityTypeId;

    /**
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(EavSetupFactory $eavSetupFactory,
                                \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory,
                                \Magento\Eav\Model\Config $eavConfig
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $customerFlag = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/customersync/syncstopflg', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $customerFlag);

        $categoryFlag = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/categorysync/syncstopflg', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $categoryFlag);

        $productFlag = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/productsync/syncstopflg', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $productFlag);

        $productImageFlag = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/productimagesync/syncstopflg', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $productImageFlag);

        $configuratorFlag = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/configuratorsync/syncstopflg', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $configuratorFlag);

        $merchandiseFlag = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/merchandise/syncstopflg', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $merchandiseFlag);

        $orderFlag = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/ordersync/syncstopflg', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $orderFlag);

        $productInventoryAndPriceFlag = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/inventorysync/syncstopflg', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $productInventoryAndPriceFlag);

        $customerStopButton = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/customersync/syncstop', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $customerStopButton);

        $categoryStopButton = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/categorysync/syncstop', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $categoryStopButton);

        $productStopButton = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/productsync/syncstop', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $productStopButton);

        $productImageStopButton = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/productimagesync/syncstop', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $productImageStopButton);

        $configuratorStopButton = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/configuratorsync/syncstop', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $configuratorStopButton);

        $merchandiseStopButton = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/merchandise/syncstop', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $merchandiseStopButton);

        $orderStopButton = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/ordersync/syncstop', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $orderStopButton);

        $productInventoryAndPriceStopButton = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/inventorysync/syncstop', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $productInventoryAndPriceStopButton);

        $backgroundSync = ['config_id' => '', 'scope' => 'default', 'scope_id' => '0', 'path' => 'amconnectorsync/background_sync/background_sync', 'value' => '1'];
        $setup->getConnection()->insert($setup->getTable('core_config_data'), $backgroundSync);

        /**
         * @var EavSetup $eavSetup
         */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'acumatica_parent_category_id',
            ['type' => 'varchar',
                'label' => 'Acumatica Parent Category Id',
                'input' => 'text',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => ''
            ]
        );
        $attributeId = $eavSetup->getAttributeId('catalog_category', 'acumatica_parent_category_id');
        $attributeSetId = $eavSetup->getAttributeSetId('catalog_category', 'Default');
        $attributeGroupId = $eavSetup->getAttributeGroupId('catalog_category', $attributeSetId, 'General Information');
        $eavSetup->addAttributeToSet('catalog_category', $attributeSetId, $attributeGroupId, $attributeId);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'acumatica_category_id',
            ['type' => 'varchar',
                'label' => 'Acumatica Category Id',
                'input' => 'text',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => ''
            ]
        );
        $attributeId = $eavSetup->getAttributeId('catalog_category', 'acumatica_category_id');
        $attributeSetId = $eavSetup->getAttributeSetId('catalog_category', 'Default');
        $attributeGroupId = $eavSetup->getAttributeGroupId('catalog_category', $attributeSetId, 'General Information');
        $eavSetup->addAttributeToSet('catalog_category', $attributeSetId, $attributeGroupId, $attributeId);
    }
}
