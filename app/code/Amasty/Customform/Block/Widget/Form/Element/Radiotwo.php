<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */

/**
 * Copyright В© 2016 Amasty. All rights reserved.
 */
namespace Amasty\Customform\Block\Widget\Form\Element;

class Radiotwo extends Radio
{
    public function _construct()
    {
        parent::_construct();

        $this->options['title'] = __('Radio v.2');
    }

    public function getLabelClassName()
    {
        return 'class="label-for-version-two"';
    }

    public function getBr()
    {
        return '';
    }
}
