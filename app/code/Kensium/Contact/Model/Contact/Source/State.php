<?php
/**
 * Kensium_Contact extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_Contact
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\Contact\Model\Contact\Source;

class State implements \Magento\Framework\Option\ArrayInterface
{
    const _EMPTY = 1;


    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => 'AL',
                'label' => __('Alabama')
            ],
             [
                'value' => 'AK',
                'label' => __('Alaska')
            ],
            [
                'value' => 'AZ',
                'label' => __('Arizona')
            ],
            [
                'value' => 'AR',
                'label' => __('Arkansas')
            ],
            [
                'value' => 'CA',
                'label' => __('California')
            ],
            [
                'value' => 'CO',
                'label' => __('Colorado')
            ],
            [
                'value' => 'CT',
                'label' => __('Connecticut')
            ],
            [
                'value' => 'DE',
                'label' => __('Delaware')
            ],
            [
                'value' => 'DC',
                'label' => __('District Of Columbia')
            ],
            [
                'value' => 'FL',
                'label' => __('Florida')
            ],
            [
                'value' => 'GA',
                'label' => __('Georgia')
            ],
            [
                'value' => 'HI',
                'label' => __('Hawaii')
            ],
            [
                'value' => 'ID',
                'label' => __('Idaho')
            ],
            [
                'value' => 'IL',
                'label' => __('Illinois')
            ],
            [
                'value' => 'IN',
                'label' => __('Indiana')
            ],
            [
                'value' => 'IA',
                'label' => __('Iowa')
            ],
            [
                'value' => 'KS',
                'label' => __('Kansas')
            ],
            [
                'value' => 'KY',
                'label' => __('Kentucky')
            ],
            [
                'value' => 'LA',
                'label' => __('Louisiana')
            ],
            [
                'value' => 'ME',
                'label' => __('Maine')
            ],
            [
                'value' => 'MD',
                'label' => __('Maryland')
            ],
            [
                'value' => 'MA',
                'label' => __('Massachusetts')
            ],
            [
                'value' => 'MI',
                'label' => __('Michigan')
            ],
            [
                'value' => 'MN',
                'label' => __('Minnesota')
            ],
            [
                'value' => 'MS',
                'label' => __('Mississippi')
            ],
            [
                'value' => 'MO',
                'label' => __('Missouri')
            ],
            [
                'value' => 'MT',
                'label' => __('Montana')
            ],
            [
                'value' => 'NE',
                'label' => __('Nebraska')
            ],
            [
                'value' => 'NV',
                'label' => __('Nevada')
            ],
            [
                'value' => 'NH',
                'label' => __('New Hampshire')
            ],
            [
                'value' => 'NJ',
                'label' => __('New Jersey')
            ],
            [
                'value' => 'NM',
                'label' => __('New Mexico')
            ],
            [
                'value' => 'NY',
                'label' => __('New York')
            ],
            [
                'value' => 'NC',
                'label' => __('North Carolina')
            ],
            [
                'value' => 'ND',
                'label' => __('North Dakota')
            ],
            [
                'value' => 'OH',
                'label' => __('Ohio')
            ],
            [
                'value' => 'OK',
                'label' => __('Oklahoma')
            ],
            [
                'value' => 'OR',
                'label' => __('Oregon')
            ],
            [
                'value' => 'PA',
                'label' => __('Pennsylvania')
            ],
            [
                'value' => 'RI',
                'label' => __('Rhode Island')
            ],
            [
                'value' => 'SC',
                'label' => __('South Carolina')
            ],
            [
                'value' => 'SD',
                'label' => __('South Dakota')
            ],
            [
                'value' => 'TN',
                'label' => __('Tennessee')
            ],
            [
                'value' => 'TX',
                'label' => __('Texas')
            ],
            [
                'value' => 'UT',
                'label' => __('Utah')
            ],
            [
                'value' => 'VT',
                'label' => __('Vermont')
            ],
            [
                'value' => 'VA',
                'label' => __('Virginia')
            ],
            [
                'value' => 'WA',
                'label' => __('Washington')
            ],
            [
                'value' => 'WV',
                'label' => __('West Virginia')
            ],
            [
                'value' => 'WI',
                'label' => __('Wisconsin')
            ],
            [
                'value' => 'WY',
                'label' => __('Wyoming')
            ],
            
        ];
        return $options;

    }
}
