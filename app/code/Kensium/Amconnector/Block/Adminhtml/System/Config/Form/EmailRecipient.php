<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Form;
/**
 *
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2015 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
use Kensium\Amconnector\Block\Adminhtml\System\Config\Fields;
use Kensium\Amconnector\Helper\Licensecheck;
use Kensium\Amconnector\Model\LicensecheckFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;

class EmailRecipient extends Fields
{
    protected $licenseHelper;

    protected $licenseFactory;

    protected $_scopeConfig;

    public function __construct(
                                Licensecheck $licenseHelper,
                                LicensecheckFactory $licenseFactory,
                                ScopeConfigInterface $scopeConfig
    ){

        $this->licenseHelper = $licenseHelper;
        $this->licenseFactory = $licenseFactory;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $configEmailRecipient = $this->licenseHelper->getAdminEmail();
        $licensekeycollection = $this->licenseFactory->create()->getCollection();
        if (count($licensekeycollection)) {
            $emailRecipientConfig = $this->_scopeConfig->getValue('license/add_domain_request/add_email_recipient');
        } else {
            $emailRecipientConfig = $this->_scopeConfig->getValue('license/license_request/email_recipient');
        }
        if ($emailRecipientConfig == '')
            return "<input type='text' class='input-text' id='license_add_domain_request_add_email_recipient' name='groups[add_domain_request][fields][add_email_recipient][value]' value='$configEmailRecipient'>";
        else
            return "<input type='text' class='input-text' id='license_add_domain_request_add_email_recipient' name='groups[add_domain_request][fields][add_email_recipient][value]' value='$emailRecipientConfig'>";

    }
}
