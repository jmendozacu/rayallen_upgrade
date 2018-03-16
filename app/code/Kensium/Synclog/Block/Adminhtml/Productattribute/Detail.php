<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\Productattribute;
use Magento\Framework\Stdlib\DateTime\Timezone;

/**
 * Class Productattributesynclogdetails
 * @package Kensium\Synclog\Block\Adminhtml\Productattribute
 */
class Detail extends \Magento\Backend\Block\Template
{

    protected $timezone;

    /**
     * @var string
     */
    protected $_template = 'productattribute/detail.phtml';

    /**
     * @var \Kensium\Synclog\Model\ProductattributesynclogFactory
     */
    protected $productattributesynclogFactory;

    /**
     * @var \Kensium\Synclog\Model\ProductattributesynclogdetailsFactory
     */
    protected $productattributesynclogdetailsFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Synclog\Model\ProductattributesynclogFactory $productattributesynclogFactory,
        \Kensium\Synclog\Model\ProductattributesynclogdetailsFactory $productattributesynclogdetailsFactory,
        TimeZone $timezone,
        $data = array()
    )
    {
        $this->productattributesynclogFactory = $productattributesynclogFactory;
        $this->productattributesynclogdetailsFactory = $productattributesynclogdetailsFactory;
        $this->timezone = $timezone;
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

    /**
     * @return mixed
     */
    function getLog()
    {
        $collection = $this->productattributesynclogFactory->create()->load($this->_request->getParam('id'));
        return $collection->getData();
    }

    /**
     * @return array
     */
    function getLogDetails() // `id` from amconnector_productattribute_log table is reference to the `sync_record_id` in the amconnector_productattribute_log_details table
    {
        $collection = $this->productattributesynclogdetailsFactory->create()->getCollection()->addFilter('sync_record_id', $this->_request->getParam('id'));
        return $collection->getData();
    }

    public function getDateObject(){
        return $this->timezone;
    }
}
