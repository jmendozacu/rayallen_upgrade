<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\ProductConfigurator;
class Detail extends \Magento\Backend\Block\Template
{
    protected $_template = 'productConfigurator/detail.phtml';
    protected $productsynclogFactory;
    protected $pproductsynclogdetailsFactory;
    protected $prodMod;
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Kensium\Synclog\Model\ProductconfiguratorsynclogFactory $productsynclogFactory,
        \Kensium\Synclog\Model\ProductconfiguratorsynclogdetailsFactory $productsynclogdetailsFactory,
        \Magento\Catalog\Model\ProductFactory $prodMod,
        $data = array()
    )
    {
        $this->productsynclogFactory = $productsynclogFactory;
        $this->productsynclogdetailsFactory = $productsynclogdetailsFactory;
        $this->prodMod = $prodMod;
        parent::__construct($context, $data = array());
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {

        // die('Preparing the layout here so please ignore this test here');

        return parent::_prepareLayout();
    }


    function getLog()
    {
        //die($this->_request->getParam('id'));
        //die(var_dump());
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
        $product = $this->prodMod->create()->load($prodId);
        return $product->getName();
    }
}
