<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\Customer;
use Magento\Framework\Stdlib\DateTime\Timezone;
class Detail extends \Magento\Backend\Block\Template
{

    protected $_template = 'customer/detail.phtml';
    protected $customersynclogFactory;
    protected $customersynclogdetailsFactory;
    protected $timezone;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Synclog\Model\CustomerFactory $customersynclogFactory,
        \Kensium\Synclog\Model\CustomersynclogdetailsFactory $customersynclogdetailsFactory,
        TimeZone $timezone,
        $data = array()
    )
    {
        $this->customersynclogFactory = $customersynclogFactory;
        $this->customersynclogdetailsFactory = $customersynclogdetailsFactory;
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
        $customerLog = $this->customersynclogFactory->create()->load($this->_request->getParam('id'));
        return $customerLog;
    }

    function getLogDetails() // `id` from amconnector_category_log table is reference to the `sync_record_id` in the amconnector_category_log_details table
    {
        $collection = $this->customersynclogdetailsFactory->create()->getCollection()->addFilter('sync_record_id', $this->_request->getParam('id'));
        return $collection->getData();
    }

    public function getDateObject(){
        return $this->timezone;
    }
}
