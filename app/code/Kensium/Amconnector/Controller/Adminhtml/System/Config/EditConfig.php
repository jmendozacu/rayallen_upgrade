<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\System\Config;

use Kensium\Amconnector\Model\ResourceModel\Licensecheck as resourceModelLicense;
/**
 * System Configuration Abstract Controller
 */
class EditConfig extends \Magento\Config\Controller\Adminhtml\System\Config\AbstractScopeConfig
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    const IS_LICENSE_INVALID = "Invalid";
    const IS_LICENSE_VALID = "Valid";
    const IS_LICENSE_DISABLED = "Disabled";
    const SECTION_COMMON = 'amconnectorcommon';
    const SECTION_TIME = 'amconnector_time_configuration';
    const SECTION_LICENSE = 'license';
    const SECTION_SYNC = 'amconnectorsync';
    const STATUS = 'sync';
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param \Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker $sectionChecker
     * @param \Magento\Config\Model\Config $backendConfig
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
         resourceModelLicense $resourceModelLicense,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker $sectionChecker,
        \Magento\Config\Model\Config $backendConfig,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $configStructure, $sectionChecker, $backendConfig);
        $this->resultPageFactory = $resultPageFactory;
        $this->resourceModelLicense = $resourceModelLicense;

    }

    /**
     * Edit configuration section
     *
     * @return \Magento\Framework\App\ResponseInterface|void
     */
    public function execute()
    {
        $current = $this->getRequest()->getParam('section');
        $website = $this->getRequest()->getParam('website');
        $store = $this->getRequest()->getParam('store');
        if($store == '')
        {
            $storeId = 1;
        }else{
            $storeId = $store;
        }
        $licenseStatus = $this->resourceModelLicense->getLicenseStatus($storeId);
        if ($licenseStatus == self::IS_LICENSE_INVALID && ($current == self::SECTION_COMMON || $current == self::SECTION_TIME || $current == self::SECTION_SYNC)) {
            $this->messageManager->addError('Invalid license key.');
            session_write_close();
            $redirectResult = $this->resultRedirectFactory->create();
            return $redirectResult->setPath('adminhtml/system_config/edit/section/license/', ['website' => $website, 'store' => $store]);
            return;

        } else if ($licenseStatus == self::IS_LICENSE_DISABLED && ($current == self::SECTION_COMMON || $current == self::SECTION_TIME || $current == self::SECTION_SYNC)) {
            $this->messageManager->addError('Your License Key is no longer valid.  Please contact Support.');
            session_write_close();
            $redirectResult = $this->resultRedirectFactory->create();
            return $redirectResult->setPath('adminhtml/system_config/edit/section/license/', ['website' => $website, 'store' => $store]);
            return;
        }

        /** @var $section \Magento\Config\Model\Config\Structure\Element\Section */
        $section = $this->_configStructure->getElement($current);
        if ($current && !$section->isVisible($website, $store)) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
            $redirectResult = $this->resultRedirectFactory->create();
            return $redirectResult->setPath('adminhtml/*/', ['website' => $website, 'store' => $store]);
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Config::system_config');
        $resultPage->getLayout()->getBlock('menu')->setAdditionalCacheKeyInfo([$current]);
        $resultPage->addBreadcrumb(__('System'), __('System'), $this->getUrl('*\/system'));
        $resultPage->getConfig()->getTitle()->prepend(__('Configuration'));
        return $resultPage;
    }
}
