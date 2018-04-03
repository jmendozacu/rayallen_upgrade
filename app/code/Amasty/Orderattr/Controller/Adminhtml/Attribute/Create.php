<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
namespace Amasty\Orderattr\Controller\Adminhtml\Attribute;

class Create extends \Amasty\Orderattr\Controller\Adminhtml\Attribute
{

    public function execute()
    {
        return $this->_forward('edit');
    }
}
