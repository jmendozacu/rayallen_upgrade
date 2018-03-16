<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Scheduler\Block\Adminhtml\Scheduler\Renderer;

use Magento\Framework\DataObject;

class ViewButton extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
        \Magento\Backend\Helper\Data $backendHelper
    )
    {
        $this->session = $session;
        $this->backendHelper = $backendHelper;
    }


    /**
     * @param DataObject $row
     * @return string|void
     */
    public function render(DataObject $row)
    {

        $url = 'synclog/grid/index/job_code/'.$row['job_code'].'/schedule_id/'.$row['id'];
        $url = $this->backendHelper->getUrl($url);
        echo "<a href='".$url."'><div style='color:#EB5202 ;text-decoration: underline;text-decoration-color:#EB5202;'>View</div></a>";
    }
}
