<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Orderstatus\Controller\Adminhtml\Orderstatus;

class NewAction extends \Kensium\Orderstatus\Controller\Adminhtml\Orderstatus
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
