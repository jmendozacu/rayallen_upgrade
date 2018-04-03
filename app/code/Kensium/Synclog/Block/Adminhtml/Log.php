<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml;

use Magento\Framework\Stdlib\DateTime\Timezone;

class Log extends \Magento\Backend\Block\Template
{

    protected $_template = '';
    protected $syncScheduleFactory;
    /**
     * @var TimeZone
     */
    protected $timezone;


    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Synclog\Model\SyncScheduleFactory $syncScheduleFactory,
	\Magento\Backend\Helper\Data $backendHelper,
        TimeZone $timezone,
        $data = array()
    )
    {
        $this->syncScheduleFactory = $syncScheduleFactory;
        $this->timezone = $timezone;
	$this->backendHelper = $backendHelper;
        parent::__construct($context, $data = array());
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {

        $jobCode = ucfirst($this->_request->getParam('job_code'));
        if ($jobCode == 'Customer') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\Customer\Grid', 'loggrid')
            );
            $this->_template = 'customer/grid.phtml';
        }
        if ($jobCode == 'Merchandise') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\Merchandise\Grid', 'loggrid')
            );
            $this->_template = 'merchandise/grid.phtml';
        }
        if ($jobCode == 'Order') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\Order\Grid', 'loggrid')
            );
            $this->_template = 'order/grid.phtml';
        }

        if ($jobCode == 'FailedOrder') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\Order\Grid', 'loggrid')
            );
            $this->_template = 'order/grid.phtml';
        }
        if ($jobCode == 'OrderShipment') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\Shipment\Grid', 'loggrid')
            );
            $this->_template = 'shipment/grid.phtml';
        }
		if ($jobCode == 'Category') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\Category\Grid', 'loggrid')
            );
            $this->_template='category/grid.phtml';
        }
        if ($jobCode == 'TaxCategory') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\TaxCategory\Grid', 'loggrid')
            );
            $this->_template = 'taxcategory/grid.phtml';
        }
        if ($jobCode == 'Productinventory') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\ProductInventory\Grid', 'loggrid')
            );
            $this->_template = 'productinventory/grid.phtml';
        }
        if ($jobCode == 'Product') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\Product\Grid', 'loggrid')
            );
            $this->_template = 'product/grid.phtml';
        }

        if ($jobCode == 'ProductConfigurator') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\ProductConfigurator\Grid', 'loggrid')
            );
            $this->_template = 'productConfigurator/grid.phtml';
        }

        if ($jobCode == 'Productimage') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\Productimage\Grid', 'loggrid')
            );
            $this->_template = 'productimage/grid.phtml';
        }
        if ($jobCode == 'Productprice') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\ProductPrice\Grid', 'loggrid')
            );
            $this->_template = 'productprice/grid.phtml';
        }

        if ($jobCode == 'Productattribute') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\Productattribute\Grid', 'loggrid')
            );
            $this->_template = 'productattribute/grid.phtml';
        }

        if ($jobCode == 'Customerattribute') {

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('Kensium\Synclog\Block\Adminhtml\Customerattribute\Grid', 'loggrid')
            );
            $this->_template = 'customerattribute/grid.phtml';
        }

        return parent::_prepareLayout();
    }


    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }


    public function getLogDetails()
    {
        $collection = $this->syncScheduleFactory->create()->load($this->_request->getParam('schedule_id'));
        return $collection->getData();
    }

    public function getDateObject()
    {
        return $this->timezone;
    }

    public function getBackUrl()
    {
	$url = $this->backendHelper->getUrl('scheduler/scheduler/index');
	return $url;	
    }
}

