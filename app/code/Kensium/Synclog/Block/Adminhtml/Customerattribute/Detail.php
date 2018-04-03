<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\Customerattribute;
use Magento\Framework\Stdlib\DateTime\Timezone;

/**
 * Class Customerattributesynclogdetails
 * @package Kensium\Synclog\Block\Adminhtml\Customerattribute
 */
class Detail extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'customerattribute/detail.phtml';
    protected $timezone;

    /**
     * @var \Kensium\Synclog\Model\CustomerattributesynclogFactory
     */
    protected $customerattributesynclogFactory;

    /**
     * @var \Kensium\Synclog\Model\CustomerattributesynclogdetailsFactory
     */
    protected $customerattributesynclogdetailsFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Synclog\Model\CustomerattributesynclogFactory $customerattributesynclogFactory,
        TimeZone $timezone,
        \Kensium\Synclog\Model\CustomerattributesynclogdetailsFactory $customerattributesynclogdetailsFactory,
        $data = array()
    )
    {
        $this->customerattributesynclogFactory = $customerattributesynclogFactory;
        $this->timezone = $timezone;
        $this->customerattributesynclogdetailsFactory = $customerattributesynclogdetailsFactory;

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
        $collection = $this->customerattributesynclogFactory->create()->load($this->_request->getParam('id'));
        return $collection->getData();
    }

    /**
     * @return array
     */
    function getLogDetails()
    {
        $collection = $this->customerattributesynclogdetailsFactory->create()->getCollection()->addFilter('sync_record_id', $this->_request->getParam('id'));
        return $collection->getData();
    }
    public function getDateObject(){
        return $this->timezone;
    }
}
