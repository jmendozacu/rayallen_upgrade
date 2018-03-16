<?php
namespace Iglobal\Stores\Model;

class Setup extends \Magento\Eav\Setup\EavSetup
{

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavEavSetupFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
    */
    protected $catalog_eav_attribute;

    /**
     * @var \Magento\Eav\Model\Entity
    */
    protected $eav_entity;

    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Eav\Model\Entity\Setup\Context $context,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\Eav\Setup\EavSetupFactory $eavEavSetupFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $catalog_eav_attribute,
        \Magento\Eav\Model\Entity $eav_entity
    ) {
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);

        $this->eavEavSetupFactory = $eavEavSetupFactory;
        $this->catalog_eav_attribute = $catalog_eav_attribute;
        $this->eav_entity = $eav_entity;
    }

    /**
     * Create an attribute.
     *
     * For reference, see Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().
     *
     * @return int|false
     */
    function createAttribute($labelText, $attributeCode, $inputType, $option = -1, $values = -1, $productTypes = -1)
    {
        $setInfo = array('SetID'=>'4', 'GroupID'=>'7');
        $labelText = trim($labelText);
        $attributeCode = trim($attributeCode);

        if($labelText == '' || $attributeCode == '')
        {
            //$this->logError("Can't import the attribute with an empty label or code.  LABEL= [$labelText]  CODE= [$attributeCode]");
            return false;
        }

        if($values === -1)
            $values = '';

        if($productTypes === -1)
            $productTypes = '';

        if($setInfo !== -1 && (isset($setInfo['SetID']) == false || isset($setInfo['GroupID']) == false))
        {
            //$this->logError("Please provide both the set-ID and the group-ID of the attribute-set if you'd like to subscribe to one.");
            return false;
        }

        //$this->logInfo("Creating attribute [$labelText] with code [$attributeCode].");

        //>>>> Build the data structure that will define the attribute. See
        //     Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().

        if($inputType === 'text'){
            $data = array(
                'is_global'                     => '1',
                'input'                         => 'text',
                'default_value_text'            => '',
                'default_value_yesno'           => '0',
                'default_value_date'            => '',
                'default_value_textarea'        => '',
                'is_unique'                     => '0',
                'required'                      => false,
                'frontend_class'                => 'validate-number',
                'is_searchable'                 => '0',
                'is_visible_in_advanced_search' => '0',
                'is_comparable'                 => '0',
                'is_used_for_promo_rules'       => '0',
                'is_html_allowed_on_front'      => '1',
                'is_visible_on_front'           => '0',
                'used_in_product_listing'       => '0',
                'used_for_sort_by'              => '0',
                'is_configurable'               => '0',
                'is_filterable'                 => '0',
                'is_filterable_in_search'       => '0',
                'backend_type'                  => 'varchar',
                'default_value'                 => '',
            );
        }
        elseif($inputType === 'select'){
            $data = array(
                'is_global'                     => '1',
                'input'                         => 'select',
                'default_value_text'            => '',
                'default_value_yesno'           => '0',
                'default_value_date'            => '',
                'default_value_textarea'        => '',
                'is_unique'                     => '0',
                'required'                      => false,
                'frontend_class'                => '',
                'is_searchable'                 => '0',
                'is_visible_in_advanced_search' => '0',
                'is_comparable'                 => '0',
                'is_used_for_promo_rules'       => '0',
                'is_html_allowed_on_front'      => '1',
                'is_visible_on_front'           => '0',
                'used_in_product_listing'       => '0',
                'used_for_sort_by'              => '0',
                'is_configurable'               => '0',
                'is_filterable'                 => '0',
                'is_filterable_in_search'       => '0',
                'backend_type'                  => 'int',
                'default_value'                 => '',
            );
        }

        // Now, overlay the incoming values on to the defaults.
        if(is_array($values)){
            foreach($values as $key => $newValue){
                if(isset($data[$key]) == false)
                {
                    //$this->logError("Attribute feature [$key] is not valid.");
                    return false;
                }

                else
                    $data[$key] = $newValue;
            }
        }

        // Valid product types: simple, grouped, configurable, virtual, bundle, downloadable, giftcard
        $data['apply_to']       = $productTypes;
        $data['label'] = $labelText;

        if($inputType == 'select' && $option !== -1){
            $data['option'] = $option;
        }

        $eavSetup = $this->eavEavSetupFactory->create(["setup" => $this->getSetup()]);
        $attr = $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode, $data);
        return $attr->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
    }
}
