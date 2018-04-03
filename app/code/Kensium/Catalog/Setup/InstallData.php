<?php
/* app/code/Kensium/Feature/Setup/InstallData.php */

namespace Kensium\Catalog\Setup;

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
     * EAV setup factory
     *
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
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
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
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav/attribute
         */

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'sale',
            [ 'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Sale',
                'input' => 'boolean',
                'class' => '',
                'source' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => '',
                'is_html_allowed_on_front' => 1
            ]
        );
        $attributeId = $eavSetup->getAttributeId('catalog_product', 'sale');
        $attributeSetId = $eavSetup->getAttributeSetId('catalog_product', 'Default');
        $attributeGroupId = $eavSetup->getAttributeGroupId('catalog_product', $attributeSetId, 'General');
        $eavSetup->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId);
    }
}