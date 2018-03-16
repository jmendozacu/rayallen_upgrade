<?php
namespace Smartwave\Porto\Model\Config\Settings\General;

class Layout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1170', 'label' => __('1170px (Default)')],
            ['value' => 'full_width', 'label' => __('Full Width')]
        ];
    }

    public function toArray()
    {
        return [
            '1170' => __('1170px (Default)'),
            'full_width' => __('Full Width')
        ];
    }
}
