<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\Product;
use Magento\Framework\Stdlib\DateTime\Timezone;

class Detail extends \Magento\Backend\Block\Template
{
    protected $_template = 'product/detail.phtml';
    protected $productsynclogFactory;
    protected $pproductsynclogdetailsFactory;
    protected $timezone;
    protected $prodMod;
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Synclog\Model\ProductFactory $productsynclogFactory,
        \Kensium\Synclog\Model\ProductsynclogdetailsFactory $productsynclogdetailsFactory,
        TimeZone $timezone,
        \Magento\Catalog\Model\Product $prodMod,
        $data = array()
    )
    {
        $this->productsynclogFactory = $productsynclogFactory;
        $this->productsynclogdetailsFactory = $productsynclogdetailsFactory;
        $this->timezone = $timezone;
        $this->prodMod = $prodMod;
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
        $collection = $this->productsynclogFactory->create()->getCollection()->addFilter('id', $this->_request->getParam('id'));
        return $collection->getData();
    }

    function getLogDetails() // `id` from amconnector_category_log table is reference to the `sync_record_id` in the amconnector_category_log_details table
    {
        $collection = $this->productsynclogdetailsFactory->create()->getCollection()->addFilter('sync_record_id', $this->_request->getParam('id'));
        return $collection->getData();
    }
    
    function getProductName($prodId)
    {
        $product = $this->prodMod->load($prodId);
        return $product->getName();
    }

    public function getDateObject(){
        return $this->timezone;
    }
}
