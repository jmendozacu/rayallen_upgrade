<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\OrderLogs\Block\Adminhtml\Log\Renderer;

use Magento\Framework\DataObject;

class OrderLink extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
	\Magento\Framework\App\ResourceConnection $resConn,
        \Magento\Backend\Helper\Data $backendHelper
    )
    {
        $this->session = $session;
        $this->backendHelper = $backendHelper;
	$this->_resConn = $resConn;
    }


    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $incrementId = $row->getData('order_id');
	$orderId = $this->_resConn->getConnection()->fetchOne("SELECT entity_id FROM sales_order WHERE increment_id = '" . $incrementId . "' ");
	$url = 'sales/order/view/order_id/' . $orderId;
        $url = $this->backendHelper->getUrl($url);
        echo "<a href='" . $url . "' target='_blank' style='text-decoration:none'>" . $incrementId . "</a>";
    }
}

