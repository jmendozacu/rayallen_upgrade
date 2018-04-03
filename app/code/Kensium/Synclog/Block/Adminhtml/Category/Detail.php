<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\Category;
use Magento\Framework\Stdlib\DateTime\Timezone;

class Detail extends \Magento\Backend\Block\Template
{
    protected $_template = 'category/detail.phtml';

    protected $timezone;
    protected $categorysynclogFactory;
    protected $categorysynclogdetailsFactory;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Synclog\Model\CategoryFactory $categoryFactory,
        TimeZone $timezone,
        \Kensium\Synclog\Model\CategorysynclogdetailsFactory $categorysynclogdetailsFactory,
        $data = array()
    )
    {
        $this->categoryFactory = $categoryFactory;
        $this->categorysynclogdetailsFactory = $categorysynclogdetailsFactory;
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
        $collection = $this->categoryFactory->create()->load($this->_request->getParam('id'));
        return $collection->getData();
    }

    function getLogDetails()
    {
        $collection = $this->categorysynclogdetailsFactory->create()->getCollection()->addFilter('sync_record_id', $this->_request->getParam('id'));
        return $collection->getData();
    }
    public function getDateObject(){
        return $this->timezone;
    }
}
