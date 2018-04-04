<?php

/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\DataFeedManager\Helper;

/**
 * Attributes management
 */
class Attributes extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_ignored_attribute_codes = ['status', 'visibility'];
    protected $_ignored_attribute_backend_models = [
        "Magento\Catalog\Model\Product\Attribute\Backend\Price" => 'Wyomind\DataFeedManager\Helper\AttributesPrices',
        "Magento\Catalog\Model\Product\Attribute\Backend\Tierprice" => 'Wyomind\DataFeedManager\Helper\AttributesPrices',
        "Magento\Weee\Model\Attribute\Backend\Weee\Tax" => "Wyomind\DataFeedManager\Helper\AttributesWeeeTax",
    ];
    protected $_attributes = [
        'Wyomind\DataFeedManager\Helper\AttributesDefault',
        'Wyomind\DataFeedManager\Helper\AttributesCategories',
        'Wyomind\DataFeedManager\Helper\AttributesImages',
        'Wyomind\DataFeedManager\Helper\AttributesInventory',
        'Wyomind\DataFeedManager\Helper\AttributesPrices',
        'Wyomind\DataFeedManager\Helper\AttributesStockInTheChannel',
        'Wyomind\DataFeedManager\Helper\AttributesUrl',
        'Wyomind\DataFeedManager\Helper\AttributesReviews'
    ];
    protected $_listOfAttributes = [];
    protected $_customVariables = [];
    protected $_attributeSetRepository = null;
    protected $_attributesCustomOptions = null;
    private $_messageManager = null;
    private $_objectManager = null;
    private $_as = [];
    public $skipProduct = false;
    private $_model = null;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
            \Magento\Eav\Model\Entity\TypeFactory $attributeTypeFactory,
            \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
            \Wyomind\DataFeedManager\Model\ResourceModel\Variables\CollectionFactory $variableCollectionFactory,
            \Magento\Eav\Model\AttributeSetRepository $attributeSetRepository,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Magento\Framework\ObjectManagerInterface $objectManager,
            \Wyomind\DataFeedManager\Helper\AttributesCustomOptions $customOptions
    )
    {


        $this->_attributesCustomOptions = $customOptions;
        $this->_attributeSetRepository = $attributeSetRepository;
        $this->_objectManager = $objectManager;

        $this->_messageManager = $messageManager;

        parent::__construct($context);

        $typeId = -1;
        $resTypeId = $attributeTypeFactory->create()->getCollection()->addFieldToFilter('entity_type_code', ['eq' => 'catalog_product']);
        foreach ($resTypeId as $re) {
            $typeId = $re['entity_type_id'];
        }
        $attributesList = $attributeFactory->create()->getCollection()->addFieldToFilter('entity_type_id', ['eq' => $typeId]);
        $this->_listOfAttributes = [];
        foreach ($attributesList as $key => $attr) {
            $this->_listOfAttributes[$attr['attribute_code']] = $attr['backend_model']?:"";
        }

        $collection = $variableCollectionFactory->create();
        $this->_customVariables = [];
        foreach ($collection as $variable) {
            $this->_customVariables[$variable->getName()] = $variable->getScript();
        }
    }

    public function setModel($model)
    {
        $this->_model = $model;
    }

    public function executePhpScripts(
    $isPreview,
            $output,
            $product
    )
    {


        if ($output == null) {
            return;
        }

        $matches = [];
        preg_match_all("/(?<script><\?php(?<php>.*)\?>)/sU", $output, $matches);

        $i = 0;
        foreach (array_values($matches["php"]) as $phpCode) {
            $val = null;

            $displayErrors = ini_get("display_errors");
            ini_set("display_errors", 1);
            try {
                $val = $this->execPhp($phpCode, $phpCode, $product);
            } catch (\Exception $e) {
                if ($isPreview) {
                    ini_set("display_errors", $displayErrors);
                    throw new \Exception("Syntax error in " . $phpCode . " : " . $e->getMessage());
                } else {
                    ini_set("display_errors", $displayErrors);
                    $this->_messageManager->addError("Syntax error in <i>" . $phpCode . "</i><br>." . $e->getMessage());
                }
            }

            ini_set("display_errors", $displayErrors);

            if (is_array($val)) {
                $val = implode(",", $val);
            }
            $output = str_replace($matches["script"][$i], $val, $output);
            $i++;
        }

        return $output;
    }

    /**
     *
     * @param type $attributeCall
     * @param type $product
     * @return type
     * @ignore_var product
     */
    public function executeAttribute(
    $attributeCall,
            $product
    )
    {


        if (is_array($attributeCall["parameters"])) {
            if (isset($attributeCall["parameters"]["if"])) {
                $ifResult = true;
                foreach ($attributeCall["parameters"]["if"] as $if) {
                    if (isset($if['alias'])) {
                        $prop = $this->_as[$if['alias']];
                    } elseif (isset($if['object'])) {
                        $prop = $this->proceed($if, [], $product);
                    } else {
                        $prop = "";
                    }
                    switch ($if['condition']) {
                        case '==':
                            $ifResult &= $prop == $if['value'];
                            break;
                        case '!=':
                            $ifResult &= $prop != $if['value'];
                            break;
                        case '>':
// return (float) $prop .">". (float) $if['value'];
                            $ifResult &= (float) $prop > (float) $if['value'];
                            break;
                        case '<':
                            $ifResult &= (float) $prop < (float) $if['value'];
                            break;
                        case '>=':
                            $ifResult &= (float) $prop >= (float) $if['value'];
                            break;
                        case '<=':
                            $ifResult &= (float) $prop <= (float) $if['value'];
                            break;
                    }
                }
                if (!$ifResult) {
                    return "";
                }
            }
        }

// retrieve the main value
        $value = $this->proceed($attributeCall, $attributeCall["parameters"], $product);


        if (isset($attributeCall["parameters"]["as"])) {
            $this->_as[$attributeCall["parameters"]["as"]] = $value;
        }

        $prefix = (!isset($attributeCall["parameters"]['prefix'])) ? '' : $attributeCall["parameters"]['prefix'];
        $suffix = (!isset($attributeCall["parameters"]['suffix'])) ? '' : $attributeCall["parameters"]['suffix'];

// apply php
        if (is_array($attributeCall["parameters"])) {
            if (isset($attributeCall["parameters"]["output"])) {
                if ($attributeCall["parameters"]["output"] == "null") {
                    return "";
                }
                if (!is_array($value)) {
                    $toExecute = str_replace('$self', "stripslashes(\"" . addslashes($value) . "\")", $attributeCall["parameters"]["output"]);
                } else {
                    $toExecute = str_replace('$self', '$value', $attributeCall["parameters"]["output"]);
                }

                if (is_numeric($toExecute)) {
                    $value = $toExecute;
                } else {
                    $value = $this->execPhp($attributeCall["originalCall"], "return " . $toExecute . ";", $product);
                }


                if ($value === false) {
                    $this->skip();
                }
            }
        }
        if (is_array($value)) {
            $value = implode(",", $value);
        }

        $value = ($value != "") ? ($prefix . $value . $suffix) : $value;

        return $value;
    }

    public function skip($skip = true)
    {
        $this->skipProduct = $skip;
    }

    public function getSkip()
    {
        return $this->skipProduct;
    }

    public function hasParent(
    $product,
            $type = "parent"
    )
    {

        return $this->_model->checkReference($type, $product);
    }

    public function getParent(
    $product,
            $type = "parent",
            $strict = false
    )
    {

        $item = $this->_model->checkReference($type, $product);
        if ($item == null && !$strict) {
            return $product;
        }
        return $item;
    }

    public function execPhp(
    $originalCall,
            $script,
            $product = null
    )
    {

        foreach ($this->_as as $key => $value) {
            $$key = $value;
        }
        try {
            return eval($script);
        } catch (\Throwable $e) {
            if ($product != null) {
                $exc = new \Exception("\nError on line:\n" . $originalCall . "\n\nExecuted script:\n" . $script . "\n\nError message:\n" . $e->getMessage() . "\n\nProduct:\n&nbsp;&nbsp;- ID: " . $product->getId() . "\n&nbsp;&nbsp;- SKU: " . $product->getData('sku'));
            } else {
                $exc = new \Exception("\nError on line:\n" . $originalCall . "\n\nExecuted script:\n" . $script . "\n\nError message:\n" . $e->getMessage());
            }
            throw $exc;
        }
    }

    public function loadOptions(
    $product,
            $options,
            $productPattern
    )
    {

        return $this->_attributesCustomOptions->loadOptions($this->_model, $product, $options, $productPattern);
    }

    public function id(
    $model,
            $options,
            $product,
            $reference
    )
    {

        unset($options);
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return "";
        }
        $value = $item->getId();
        return $value;
    }

    public function inc(
    $model,
            $options,
            $product,
            $reference
    )
    {

        unset($options);
        unset($product);
        unset($reference);
        return $model->inc;
    }

    public function status(
    $model,
            $options,
            $product,
            $reference
    )
    {

        unset($options);
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return "";
        }

        return $item->getStatus();
    }

    public function attributeSet(
    $model,
            $options,
            $product,
            $reference
    )
    {

        unset($options);
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return "";
        }
        $attributeSetId = $item->getAttributeSetId();
        $attributeSet = $this->_attributeSetRepository->get($attributeSetId);
        if ($attributeSet != null) {
            return $attributeSet->getAttributeSetName();
        } else {
            return "";
        }
    }

    public function relationShip(
    $model,
            $options,
            $product,
            $reference
    )
    {


        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return "";
        }

        $separator = (!isset($options["separator"])) ? "," : $options["separator"];


        if (isset($model->productRelationShip[$item->getId()])) {
            $value = str_replace(">>>", $separator, $model->productRelationShip[$item->getId()]);
        }

        return $value;
    }

    public function isProductAttribute($attribute)
    {
        if (!isset($this->_listOfAttributes[$attribute]) || in_array($attribute, $this->_ignored_attribute_codes) || (isset($this->_listOfAttributes[$attribute]) && in_array($this->_listOfAttributes[$attribute], array_keys($this->_ignored_attribute_backend_models)))) {
            return false;
        } else {
            return true;
        }

        return false;
    }

    public function exists($method)
    {
        foreach ($this->_attributes as $library) {
            if (method_exists($library, $method)) {
                return true;
            }
        }
        return false;
    }

    public function proceed(
    $attributeCall,
            $options,
            $product
    )
    {

        $reference = $attributeCall['object'];

        // product attributes
        if ($this->isProductAttribute($attributeCall['property'])) {
            return $this->productAttribute($attributeCall['property'], $product, $reference);
        } else {

            $exploded = explode('_', $attributeCall['property']);
            $method = "";
            foreach ($exploded as $x) {
                $method .= ucfirst(strtolower($x));
            }
            $method = lcfirst($method);

            // specific backend models
            if (isset($this->_listOfAttributes[$attributeCall['property']])) {
                $backendModel = $this->_listOfAttributes[$attributeCall['property']];
                if (isset($this->_ignored_attribute_backend_models[$backendModel])) {
                    $backendModelHelper = $this->_ignored_attribute_backend_models[$backendModel];
                    if (method_exists($backendModelHelper, $method)) {
                        return $this->_objectManager->get($backendModelHelper)->$method($this->_model, $options, $product, $reference);
                    } else {
                        return $this->_objectManager->get($backendModelHelper)->proceedGeneric($attributeCall, $this->_model, $options, $product, $reference);
                    }
                }
            }

            // internal variables
            if (method_exists($this, $method)) {
                return $this->$method($this->_model, $options, $product, $reference);
            } else {
                foreach ($this->_attributes as $library) {
                    if (method_exists($library, $method)) {
                        return $this->_objectManager->get($library)->$method($this->_model, $options, $product, $reference);
                    }
                }
            }

// product custom options merge

            if ($attributeCall['object'] == "custom_options" && $attributeCall['property'] == "merge") {
                return $this->_attributesCustomOptions->merge($this->_model, $options, $product, $reference);
            }


// custom variables

            if (array_key_exists($attributeCall['property'], $this->_customVariables)) {
                $product = $this->_model->checkReference($reference, $product);
                if ($product == null) {
                    return "";
                }
                $toExecute = trim($this->_customVariables[$attributeCall['property']]);
                $toExecute = str_replace(['<?php', '?>'], '', $toExecute);
                $value = $this->execPhp($toExecute, $toExecute, $product);
                return $value;
            }
        }

        return false;
    }

    /**
     * All other attributes processing
     * @param \Wyomind\DataFeedManager\Model\Feeds $model
     * @param \Magento\Catalog\Model\Product            $product
     * @param string                                    $reference
     * @return string the attribute value
     */
    public function productAttribute(
    $attribute,
            $product,
            $reference
    )
    {

        $item = $this->_model->checkReference($reference, $product);
        if ($item == null) {
            return "";
        }

        $exploded = explode('_', $attribute);
        $method = "";
        foreach ($exploded as $x) {
            $method .= ucfirst(strtolower($x));
        }
        $methodName = "get" . str_replace(' ', '', ucfirst(trim($method)));
        if (in_array($attribute, $this->_model->listOfAttributes)) {
            if (in_array($this->_model->listOfAttributesType[$attribute], ['select', 'multiselect'])) {
                $val = $item->$methodName();
                if ($val == "") {
                    $val = $item->getData($attribute);
                }
                $vals = explode(',', $val);
                /* multiselect */
                if (count($vals) > 1) {
                    $value = [];
                    foreach ($vals as $v) {
                        if (isset($this->_model->attributesLabelsList[$v][$this->_model->params['store_id']])) {
                            $value[] = $this->_model->attributesLabelsList[$v][$this->_model->params['store_id']];
                        } else {
                            if (isset($this->_model->attributesLabelsList[$v][0])) {
                                $value[] = $this->_model->attributesLabelsList[$v][0];
                            }
                        }
                    }
                } else { /* select */
                    if (isset($this->_model->attributesLabelsList[$vals[0]][$this->_model->params['store_id']])) {
                        $value = $this->_model->attributesLabelsList[$vals[0]][$this->_model->params['store_id']];
                    } else {
                        if (isset($this->_model->attributesLabelsList[$vals[0]][0])) {
                            $value = $this->_model->attributesLabelsList[$vals[0]][0];
                        }
                    }
                }
            } else {
                $value = $item->$methodName();
                if ($value == "") {
                    $value = $item->getData($attribute);
                }
            }
        }
        /* Recuperer une valeur de taux de change */
        if (isset($this->_model->listOfCurrencies[$attribute])) {
            $value = $this->_model->listOfCurrencies[$attribute];
        }

        if (!isset($value)) {
            $value = "";
        }

        /* enlever les caractères invalides */
        $valueCleaned = preg_replace(
                '/' .
                '[\x00-\x1F\x7F]' .
                '|[\x00-\x7F][\x80-\xBF]+' .
                '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
                '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
                '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|' .
                '(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})' .
                '/S', ' ', $value
        );
        $value = str_replace('&#153;', '', $valueCleaned);

        return $value;
    }

    /**
     * Compare two arrays
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function cmpArray(
    $a,
            $b
    )
    {

        if (strlen(implode('', $a)) == strlen(implode('', $b))) {
            return 0;
        }
        return (strlen(implode('', $a)) < strlen(implode('', $b))) ? -1 : 1;
    }

    /**
     * Compare two strings
     * @param string $a
     * @param string $b
     * @return int
     */
    public static function cmp(
    $a,
            $b
    )
    {

        if (strlen($a) == strlen($b)) {
            return 0;
        }
        return (strlen($a) < strlen($b)) ? 1 : -1;
    }

}
