<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\ProductInventory;
use Magento\Framework\Stdlib\DateTime\Timezone;

class Detail extends \Magento\Backend\Block\Template
{
    protected $_template = 'productinventory/detail.phtml';
    protected $timezone;
    protected $productinventorysynclogFactory;
    protected $pproductinventorysynclogdetailsFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Synclog\Model\ProductinventorysynclogFactory $productinventorysynclogFactory,
        \Kensium\Synclog\Model\ProductinventorysynclogdetailsFactory $productinventorysynclogdetailsFactory,
        TimeZone $timezone,
        $data = array()
    )
    {
        $this->productinventorysynclogFactory = $productinventorysynclogFactory;
        $this->productinventorysynclogdetailsFactory = $productinventorysynclogdetailsFactory;
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


    function getLog()
    {
        $collection = $this->productinventorysynclogFactory->create()->load($this->_request->getParam('id'));
        return $collection->getData();
    }

    function getLogDetails()
    {
        $collection = $this->productinventorysynclogdetailsFactory->create()->getCollection()->addFilter('sync_record_id', $this->_request->getParam('id'));
        return $collection->getData();
    }

    public function getDateObject(){
        return $this->timezone;
    }
}
