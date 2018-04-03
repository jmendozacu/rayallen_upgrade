<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Block\Data\Form\Element;


class Boolean extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Boolean
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setValues([
            ['label' => __(' '),   'value' => ''],
            ['label' => __('No'),  'value' => '0'],
            ['label' => __('Yes'), 'value' => '1']
        ]);
    }
    
}
