<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Kensium\Amconnector\Helper\Licensecheck;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Backend\Model\Auth\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Setup\Model\License;
use Kensium\Amconnector\Block\Adminhtml\System\Config\Fields;

class MacId extends Fields
{
    protected $helperLicense;

    public function __construct(Licensecheck $helperLicense){
        $this->helperLicense = $helperLicense;
    }
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $macIds = $this->helperLicense->getMacLinux();
        $html = "<span class='macidsrequest'>".implode(",<br>",$macIds)."</span>";
        $html .= "<select multiple style='opacity:0;height:0;' id='license_add_domain_request_add_macids' name='groups[add_domain_request][fields][add_macids][value]'>";
        foreach ($macIds as $key => $value)
        {
            $html .= "<option value=".$value." selected>".$value."</option>";
        }
        $html .= "</select>";
        return $html;
    }
}