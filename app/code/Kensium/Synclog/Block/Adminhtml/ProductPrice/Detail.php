<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\ProductPrice;
class Detail extends \Magento\Backend\Block\Template
{

    protected $_template = 'productprice/detail.phtml';
    protected $productsynclogFactory;
    protected $productsynclogdetailsFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Synclog\Model\ProductpricesynclogFactory $productpricesynclogFactory,
        \Kensium\Synclog\Model\ProductpricesynclogdetailsFactory $productpricesynclogdetailsFactory,
        $data = array()
    )
    {
        $this->productpricesynclogFactory = $productpricesynclogFactory;
        $this->productsynclogdetailsFactory = $productpricesynclogdetailsFactory;
        parent::__construct($context, $data = array());
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }


    function getLog()
    {
        $productPriceLog = $this->productpricesynclogFactory->create()->load($this->_request->getParam('id'));
        return $productPriceLog;
    }

    function getLogDetails() // `id` from amconnector_category_log table is reference to the `sync_record_id` in the amconnector_category_log_details table
    {
        $collection = $this->productsynclogdetailsFactory->create()->getCollection()->addFilter('sync_record_id', $this->_request->getParam('id'));
        return $collection->getData();
    }
}
