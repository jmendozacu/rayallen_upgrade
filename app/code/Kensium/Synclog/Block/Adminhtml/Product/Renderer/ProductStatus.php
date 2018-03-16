<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Synclog\Block\Adminhtml\Product\Renderer;

use Magento\Framework\DataObject;

class ProductStatus extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    protected $prodMod;
    /**
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\Product $prodMod
    )
    {
        $this->session = $session;
        $this->backendHelper = $backendHelper;
        $this->prodMod = $prodMod;
    }


    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $rowData = $row->getData();
        $product = $this->prodMod->load($rowData['product_id']);
        $status = '';
        if($product)
        {
            $statusId = $product->getStatus();
            if ($statusId == 1) {
                $status = 'Enable';

            } else {
                $status = 'Disable';
            }
        }
        return $status;
    }
}
