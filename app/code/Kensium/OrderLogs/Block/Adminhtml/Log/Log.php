<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\OrderLogs\Block\Adminhtml\Log;
class Log extends \Magento\Backend\Block\Template
{

    protected $_template = 'log/grid.phtml';
    
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        $data = array()
    )
    {

        parent::__construct($context, $data = array());
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
    
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Kensium\OrderLogs\Block\Adminhtml\Log\Grid', 'kensium.log.grid')
        );
        
        return parent::_prepareLayout();
    }


    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        //die('This is the grid data here');
        return $this->getChildHtml('grid');
    }
}
