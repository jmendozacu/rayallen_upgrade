<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

/**
 * Csvenvelopes configuration/source model
 */
namespace Kensium\Csvenvelopes\Model;

class Config implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Display mode "fixed" flag
     *
     */
    const CSVENVELOPES_WIDGET_DISPLAY_FIXED = 'fixed';

    /**
     * Display mode "salesrule" flag
     *
     */
    const CSVENVELOPES_WIDGET_DISPLAY_SALESRULE = 'salesrule';

    /**
     * Display mode "catalogrule" flag
     *
     */
    const CSVENVELOPES_WIDGET_DISPLAY_CATALOGRULE = 'catalogrule';

    /**
     * @var array
     */
    protected $_csvenvelopesTypes = [];

    /**
     * @param array $csvenvelopesTypes
     */
    public function __construct(array $csvenvelopesTypes = [])
    {
        $this->_csvenvelopesTypes = $csvenvelopesTypes;
    }

    /**
     * Csvenvelopes types getter
     * Invokes translations to labels.
     *
     * @param bool $sorted
     * @param bool $withEmpty
     * @return array
     */
    public function getTypes($sorted = true, $withEmpty = false)
    {
        $result = [];
        foreach ($this->_csvenvelopesTypes as $type => $label) {
            $result[$type] = __($label);
        }
        if ($sorted) {
            asort($result);
        }
        if ($withEmpty) {
            return array_merge(['' => __('-- None --')], $result);
        }
        return $result;
    }

    /**
     * Get types as a source model result
     *
     * @param bool $simplified
     * @param bool $withEmpty
     * @return array
     */
    public function toOptionArray($simplified = false, $withEmpty = true)
    {
        $types = $this->getTypes(true, $withEmpty);
        if ($simplified) {
            return $types;
        }
        $result = [];
        foreach ($types as $key => $label) {
            $result[] = ['value' => $key, 'label' => $label];
        }
        return $result;
    }

    /**
     * Check provided types string as comma-separated against available types
     *
     * @param string|array $types
     * @return array
     */
    public function explodeTypes($types)
    {
        $availableTypes = $this->getTypes(false);
        $result = [];
        if ($types) {
            if (is_string($types)) {
                $types = explode(',', $types);
            }
            foreach ($types as $type) {
                if (isset($availableTypes[$type])) {
                    $result[] = $type;
                }
            }
        }
        return $result;
    }
}
