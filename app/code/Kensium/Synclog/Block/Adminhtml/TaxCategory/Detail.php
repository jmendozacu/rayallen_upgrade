<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\TaxCategory;
use Magento\Framework\Stdlib\DateTime\Timezone;

class Detail extends \Magento\Backend\Block\Template
{
    protected $_template = 'taxcategory/detail.phtml';
    protected $timezone;

    /**
     * @var \Kensium\Synclog\Model\TaxcategorysynclogFactory
     */
    protected $taxcategorysynclogFactory;

    /**
     * @var \Kensium\Synclog\Model\TaxcategorysynclogdetailsFactory
     */
    protected $taxcategorysynclogdetailsFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Synclog\Model\TaxcategorysynclogFactory $taxcategorysynclogFactory,
        TimeZone $timezone,
        \Kensium\Synclog\Model\TaxcategorysynclogdetailsFactory $taxcategorysynclogdetailsFactory,
        $data = array()
    )
    {
        $this->taxcategorysynclogFactory = $taxcategorysynclogFactory;
        $this->taxcategorysynclogdetailsFactory = $taxcategorysynclogdetailsFactory;
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
        $collection = $this->taxcategorysynclogFactory->create()->load($this->_request->getParam('id'));
        return $collection->getData();
    }

    function getLogDetails() // `id` from amconnector_category_log table is reference to the `sync_record_id` in the amconnector_category_log_details table
    {
        $collection = $this->taxcategorysynclogdetailsFactory->create()->getCollection()->addFilter('sync_record_id', $this->_request->getParam('id'));
        return $collection->getData();
    }
    public function getDateObject(){
        return $this->timezone;
    }
}
