<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Csvenvelopes\Controller\Adminhtml\Csvenvelopes;

class NewAction extends \Kensium\Csvenvelopes\Controller\Adminhtml\Csvenvelopes
{
    /**
     * Create new csvenvelopes
     *
     * @return void
     */
    public function execute()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
}
