<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\Shipment;
use Magento\Framework\Stdlib\DateTime\Timezone;

class Detail extends \Magento\Backend\Block\Template
{
    protected $timezone;
    protected $_template = 'shipment/detail.phtml';
    protected $ordersynclogFactory;
    protected $ordersynclogdetailsFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Synclog\Model\ShipmentFactory $ordersynclogFactory,
        TimeZone $timezone,
        \Kensium\Synclog\Model\ShipmentsynclogdetailsFactory $ordersynclogdetailsFactory,
        $data = array()
    )
    {
        $this->ordersynclogFactory = $ordersynclogFactory;
        $this->ordersynclogdetailsFactory = $ordersynclogdetailsFactory;
        $this->timezone = $timezone;

        parent::__construct($context, $data = array());
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {

        //die('Preparing the layout here so please ignore this test here');

        return parent::_prepareLayout();
    }


    function getLog()
    {
        $collection = $this->ordersynclogFactory->create()->getCollection()->addFilter('id', $this->_request->getParam('id'));
        return $collection->getData();
    }

    function getLogDetails() // `id` from amconnector_category_log table is reference to the `sync_record_id` in the amconnector_category_log_details table
    {
        $collection = $this->ordersynclogdetailsFactory->create()->getCollection()->addFilter('sync_record_id', $this->_request->getParam('id'));
        return $collection->getData();
    }
    public function getDateObject(){
        return $this->timezone;
    }
}
