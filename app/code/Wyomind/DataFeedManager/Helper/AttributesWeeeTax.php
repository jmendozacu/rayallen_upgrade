<?php

/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\DataFeedManager\Helper;

/**
 * Attributes management
 */
class AttributesWeeeTax extends \Wyomind\DataFeedManager\Helper\AttributesPrices implements \Wyomind\DataFeedManager\Helper\AttributesInterface
{

    /**
     * Retrieve a fixed tax rate attribute value
     * @param type $attributeCall
     * @param type $model
     * @param type $options
     * @param type $product
     * @param type $reference
     * @return string
     * @throws \Exception
     */
    public function proceedGeneric($attributeCall,
            $model,
            $options,
            $product,
            $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return "";
        }
        
        
        $attribute = $attributeCall['property'];
        $country = (!isset($options['country'])) ? false : $options['country'];
        $state = (!isset($options['state'])) ? "" : $options['state'];
        if ($state == "*") {
            $state = "";
        }
        
        
        if (!$country) {
            throw new \Exception(__("The 'country' parameter is required for a fixed rate tax product attribute"));
        }
       
        if (isset($model->weeeTaxes[$attribute][$item->getEntityId()][$country][$state])) {
            $value = $model->weeeTaxes[$attribute][$item->getEntityId()][$country][$state];
            $value = $this->applyTaxThenCurrency($model, $item->getTaxClassId(), $value, $options, $reference);
            return $value;
        } else {
            return "";
        }
    }
    
}
