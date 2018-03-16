<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Scheduler\Block\Adminhtml;
class Scheduler extends \Magento\Backend\Block\Template
{

    protected $_template = 'cron/grid.phtml';
    
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
            $this->getLayout()->createBlock('Kensium\Scheduler\Block\Adminhtml\Scheduler\Grid', 'kensium.synclogs.grid')
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
