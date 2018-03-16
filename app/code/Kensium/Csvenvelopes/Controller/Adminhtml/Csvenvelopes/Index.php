<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Csvenvelopes\Controller\Adminhtml\Csvenvelopes;

class Index extends \Kensium\Csvenvelopes\Controller\Adminhtml\Csvenvelopes
{
    /**
     * Csvenvelopess list
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Kensium_Csvenvelopes::kensium_csvenvelopes');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Csvenvelopes'));
        $this->_view->renderLayout();
    }
}
