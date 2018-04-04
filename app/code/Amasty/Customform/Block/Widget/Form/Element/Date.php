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

class Date extends AbstractElement
{
    public function _construct()
    {
        parent::_construct();
        $this->options['title'] = __('Date');
        $this->options['image_href'] = 'Amasty_Customform::images/date.png';
    }

    public function generateContent()
    {
        return '<input class="form-control" type="date"/>';
    }
}
