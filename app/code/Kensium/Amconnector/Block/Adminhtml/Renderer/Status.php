<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Block\Adminhtml\Renderer;

use Magento\Framework\DataObject;

class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Customer\Collection
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var \Kensium\Amconnector\Model\ResourceModel\Sync
     */
    protected $syncFactory;

    /**
     * @param \Magento\Backend\Model\Session $session
     * @param \Kensium\Amconnector\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Kensium\Amconnector\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Kensium\Amconnector\Model\SyncFactory $syncFactory,
        \Magento\Backend\Helper\Data $backendHelper
    )
    {
        $this->session = $session;
        $this->collectionFactory = $collectionFactory;
        $this->syncFactory = $syncFactory;
        $this->backendHelper = $backendHelper;
    }


    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $data = $row->getData();
        $syncId = $data['id'];
        $status = $this->syncFactory->create()->load($data['id'])->getData('status');
        switch ($status) {
            case "SUCCESS":
                $statusDisplay = "<span class='popupsuccsess'>" . strtoupper($status) . "</span>";
                break;
            case "ERROR":
                $statusDisplay = "<span class='popuperror'>" . strtoupper($status) . "</span>";
                break;
            case "NOTICE":
                $statusDisplay = "<span class='popupprocessing'>" . strtoupper($status) . "</span>";
                break;
            case "STARTED":
                $statusDisplay = "<span class='popupprocessing'>" . strtoupper($status) . "</span>";
                break;
            case "PROCESSING":
                $statusDisplay = "<span class='popupprocessing'>" . strtoupper($status) . "</span>";
                break;
            case "FILE NOT EXISTS":
                $statusDisplay = "<span class='popuperror'>" . strtoupper($status) . "</span>";
                break;
            case 0:
                $statusDisplay = "<span class='popupdefault'> NOT SYNC </span>";
                break;
            default:
                $statusDisplay = "<span class='popupdefault'>" . strtoupper($status) . "</span>";

        }

        return $statusDisplay;
    }
}
