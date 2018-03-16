<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\Productimage;

/**
 * Class Detail
 * @package Kensium\Synclog\Block\Adminhtml\Productimage
 */
class Detail extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'productimage/detail.phtml';

    /**
     * @var \Kensium\Synclog\Model\ProductimagesynclogFactory
     */
    protected $productimagesynclogFactory;

    /**
     * @var \Kensium\Synclog\Model\ProductimagesynclogdetailsFactory
     */
    protected $productimagesynclogdetailsFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Kensium\Synclog\Model\ProductimagesynclogFactory $productimagesynclogFactory
     * @param \Kensium\Synclog\Model\ProductimagesynclogdetailsFactory $productimagesynclogdetailsFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Synclog\Model\ProductimagesynclogFactory $productimagesynclogFactory,
        \Kensium\Synclog\Model\ProductimagesynclogdetailsFactory $productimagesynclogdetailsFactory,
        $data = array()
    )
    {
        $this->productimagesynclogFactory = $productimagesynclogFactory;
        $this->productimagesynclogdetailsFactory = $productimagesynclogdetailsFactory;

        parent::__construct($context, $data = array());
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {

        //* 'Preparing the layout here so please ignore this test here'*/

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    function getLog()
    {
        $collection = $this->productimagesynclogFactory->create()->load($this->_request->getParam('id'));
        return $collection->getData();
    }

    /**
     * @return array
     */
    function getLogDetails()
    {
        $collection = $this->productimagesynclogdetailsFactory->create()->getCollection()->addFilter('sync_record_id', $this->_request->getParam('id'));
        return $collection->getData();
    }
}
