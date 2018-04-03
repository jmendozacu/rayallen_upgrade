<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\HTTP\PhpEnvironment\ServerAddress;
use Kensium\Amconnector\Model\ResourceModel\Licensecheck as resourceModelLicense;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Setup\Model\License;
use Kensium\Amconnector\Block\Adminhtml\System\Config\Fields;
use Kensium\Amconnector\Helper\Licensecheck;

class Key extends Fields
{

    protected  $storeManagerInterface;

    protected  $resource;

    protected $resourceModelLicense;

    /**
     * @var DateTime
     */
    protected $date;
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\ServerAddress
     **/
    protected $_serverAddress;

    protected $connection;

    protected  $_scopeConfig;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    protected $licenseHelper;

    public function __construct(StoreManagerInterface $storeManagerInterface,
                                resourceModelLicense $resourceModelLicense,
                                ServerAddress $_serverAddress,
                                Config $config,
                                ResourceConnection $resource,
                                DateTime $date,
                                ScopeConfigInterface $scopeConfig,
                                Session $adminSession,
                                Licensecheck $licenseHelper,
                                \Magento\Framework\App\Request\Http $request
    ){
        $this->storeManagerInterface = $storeManagerInterface;
        $this->resourceModelLicense = $resourceModelLicense;
        $this->_serverAddress = $_serverAddress;
        $this->config = $config;
        $this->resource = $resource;
        $this->date = $date;
        $this->_scopeConfig = $scopeConfig;
        $this->_adminSession = $adminSession;
        $this->licenseHelper = $licenseHelper;
        $this->request = $request;
    }


    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $store_id = $this->request->getParam('store');
        //$store_id = $this->licenseHelper->getCurrentScopeStoreId();
        $key = $this->resourceModelLicense->getLicenseKey($store_id);
        $licenseKey = base64_decode($key);
        $html = "<input type='text'  id='licence_keys' value='" . $licenseKey . "' name='licence_key' class='input-text'>";
        $html .= "<input type='hidden' id='current_store_id' value='" . $store_id . "' name='current_store_id'>";
        return $html;
    }

}
