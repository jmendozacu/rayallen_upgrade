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

class Details extends Fields
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

        //$store_id = $this->licenseHelper->getCurrentScopeStoreId();
        $store_id = $this->request->getParam('store');
        $response = $this->resourceModelLicense->getResponse($store_id);
        if ($response == '') {
            return '';
        } else {
            $data = json_decode(base64_decode($response));
            $IsExpiryDateDefined = $data->LicenseDetails['0']->IsExpiryDateDefined;
            $DateExpires = $data->LicenseDetails['0']->DateExpires;
            $customData = $data->LicenseDetails['0']->CustomData;
            $customDataExplode = explode('||-||', $customData);
            foreach ($customDataExplode as $licenseInfo) {
                if(strpos($licenseInfo, "LicenseTypeValue")!== false){
                    $licenseTypeValue = str_replace('LicenseTypeValue=', '', $licenseInfo);
                }elseif(strpos($licenseInfo, "IPAddress")!== false){
                    $ipAddress = str_replace('IPAddress=', '', $licenseInfo);//Ip Address
                }elseif(strpos($licenseInfo, "MacAddress")!== false){
                    $macIdStr = str_replace('MacAddress=', '', $licenseInfo);//Mac Id (comma separated)
                    $macIds = explode(',', $macIdStr);
                }elseif(strpos($licenseInfo, "DomainName")!== false){
                    $domainStr = str_replace('DomainName=', '', $licenseInfo);//domains (comma separated)
                    $domainsExplode = explode(',', $domainStr);
                }elseif(strpos($licenseInfo, "Storeview")!== false){
                    $storeStr = str_replace('Storeview=', '', $licenseInfo);//domains (comma separated)
                    $storeViews = explode(',', $storeStr);
                }
            }

            $originalStoreViews = array();
            foreach($storeViews as $storeView){
            		if ((array_search($storeView, $domainsExplode)) === false) {
            		    $originalStoreViews[] = $storeView;
              	}
            }
            if ($licenseTypeValue == 1) {
                $licenseTypeValue = 'Annual';
            } else if ($licenseTypeValue == 2) {
                $licenseTypeValue = 'Perpetual';
            } else {
                $licenseTypeValue = 'Trial';
            }
            if ($IsExpiryDateDefined == 1) {
                $expirationDate = date('m/d/Y H:i:s', strtotime($DateExpires));

            } else {
                $expirationDate = '';
            }

            $strDomain = '';
            foreach ($domainsExplode as $domain) {
                $strDomain .= $domain . '<br>';
            }

            $strMac = '';
            foreach ($macIds as $macId) {
                $strMac .= $macId . '<br>';
            }

            $html = "<tr><td>Domain Name :</td> <td>$strDomain</td></tr>";
            $html .= "<tr><td>Store Views :</td> <td>".implode(',',$originalStoreViews)."</td></tr>";
            $html .= "<tr><td>IP Address :</td> <td>$ipAddress</td></tr>";
            $html .= "<tr><td>MAC ID :</td> <td>$strMac</td></tr>";
            $html .= "<tr><td>License Type:</td> <td>$licenseTypeValue</td>";
            if ($expirationDate != '') {
                $html .= "<tr><td>Expiration Date :</td> <td>$expirationDate</td>";
            }
            return $html;
        }
    }
}
