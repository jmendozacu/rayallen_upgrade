<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Synclog\Block\Adminhtml\Category\Renderer;

use Magento\Framework\DataObject;

class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\CategoryFactory  $categoryCollectionFactory
    )
    {
        $this->session = $session;
        $this->backendHelper = $backendHelper;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }


    /**
     * @param DataObject $row
     * @return string|void
     */
    public function render(DataObject $row)
    {
        $categoryCollection =  $this->categoryCollectionFactory->create()->load($row->getCatId());
        if($categoryCollection->getIsActive() == 1){
            echo "Enabled";
        }else{
            echo "Disabled";
        }
    }
}
?>