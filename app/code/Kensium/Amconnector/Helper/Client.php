<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Client extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var
     */
    public $soapClient;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Kensium\Amconnector\Helper\Url
     */
    protected $urlHelper;
    protected $syncResourceModel;

    protected $client = null;

    /**
     * @param Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param Url $urlHelper
     * @param \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Kensium\Amconnector\Model\ResourceModel\Sync $syncResourceModel
    )
    {
        parent::__construct($context);
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->logger = $context->getLogger();
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->urlHelper = $urlHelper;
        $this->syncResourceModel = $syncResourceModel;
    }

    /**
     * @param array $params
     * @param $serverUrl
     * @param null $branch
     * @return array|int
     */
    public function login($params = array(), $serverUrl,$storeId = 0)
    {
        $response['message'] = 'ERROR in soap call';
        try {
            $name = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/userName','stores',$storeId);
            if(!isset($name))
                $name = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/userName',NULL,NULL);
            $pass = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/password','stores',$storeId);
            if(!isset($pass))
                $pass = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/password',NULL,NULL);
            $company = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/companyName','stores',$storeId);
            if(!isset($company))
            	$company = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/companyName',NULL,NULL);
            if (empty($params)) {
                if(isset($company) && $company != '')
                {
                    $userName = $name;
                }else{
                    $userName = $name;
                }
                $request = array(
                    'name' => $userName,
                    'password' => $pass,
                    'company' => $company,
                    'locale' => 'en-gb'
                );
            } else {
                $request = $params;
		if(isset($request['company'])){
		  $company = $request['company'];
		}else{
		$company = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/companyName','stores',$storeId);
                 if(!isset($company))
                 $company = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/companyName',NULL,NULL);
		}
            }
            /*if (isset($branch) && !empty($branch)) {
                $request['branch'] = $branch;
            }*/

            if ($serverUrl == "") {
                $url = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl','stores',$storeId);
                if(!isset($url))
                    $url = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
            } else {
                $url = $serverUrl;
            }
	if (strpos($url,'company') == false) {
            $url .= '&company='.$company;
            }

            if (!extension_loaded('soap')) {
                $this->logger->critical(new \Magento\Framework\Exception\LocalizedException(__('PHP SOAP extension is required.')));
                return $response;
            }

            $client = new \SoapClient($url, array('trace' => 1, 'exceptions' => true));
            $client->__soapCall('Login', array($request));
            $this->soapClient = $client;
            $response = $client->__getLastResponseHeaders();

            $result = array();
            $cookies = array();
            preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $cookies);
            foreach ($cookies[1] as $value) {

                $pos = strpos($value, 'ASP.NET_SessionId');
                if ($pos !== false) {
                    $result['asp_session_id'] = str_replace('ASP.NET_SessionId=', '', $value);
                }

                $pos1 = strpos($value, '.ASPXAUTH');
                if ($pos1 !== false) {
                    $result['aspx_auth'] = str_replace('.ASPXAUTH=', '', $value);
                }

                $pos2 = strpos($value, 'UserBranch');
                if ($pos2 !== false) {
                    $result['userBranch'] = str_replace('UserBranch=', '', $value);
                }

                $pos3 = strpos($value, 'Locale');
                if ($pos3 !== false) {
                    $result['locale'] = str_replace('Locale=', '', $value);
                }
            }
            return $result;
        } catch (SoapFault $e) {
            echo $e->getMessage();
            $err = 0;
            return $err;
        }
    }

    /**
     * @param $url
     * @param $cookies
     * @return AmconnectorSoap
     */
    public function setClientCookie($url, $cookies)
    {
	if (strpos($url,'company') == false) {
            $storeId = 0;
            $company = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/companyName','stores',$storeId);
           if(!isset($company)){
               $company = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/companyName',NULL,NULL);
            }
               $url .= '&company='.$company;
         }
        $client = new AmconnectorSoap($url, array(
            'cache_wsdl' => WSDL_CACHE_NONE,
            'cache_ttl' => 86400,
            'trace' => true,
            'exceptions' => true,
        ));

        $client->__setCookie('ASP.NET_SessionId', $cookies['asp_session_id']);
        $client->__setCookie('UserBranch', $cookies['userBranch']);
        $client->__setCookie('Locale', $cookies['locale']);
        $client->__setCookie('.ASPXAUTH', $cookies['aspx_auth']);
        return $client;
    }


    /**
     * @param $XMLGetRequest
     * @param $loginUrl
     * @param $action
     * @param null $branch
     * @return \SimpleXMLElement|string
     */
    public function getAcumaticaResponse($XMLGetRequest,$loginUrl, $action, $storeId = 0, $branch = NULL,$interface = NULL)
    {
        $xml = '';
        $cookies = array();
        try {
	     $location = str_replace('?wsdl', '', $loginUrl);
          if (strpos($loginUrl,'Soap') == false) {
            if (strpos($loginUrl,'company') == false) {
              $company = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/companyName','stores',$storeId);
                if(!isset($company)){
                  $company = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/amconnectoracucon/companyName',NULL,NULL);
                }
	       $company = str_replace(' ','%20',$company);
               $location .= '?company='.$company;
            }
           }
            if (!is_object($this->client) || $branch != NULL || $interface != NULL) {
                $cookies = $this->login(array(), $loginUrl,$storeId);
                $this->client = $this->setClientCookie($loginUrl, $cookies);
            }
            //$location = str_replace('?wsdl', '', $loginUrl);
            $flag = '';                      
            $result = $this->client->__mySoapRequest($XMLGetRequest, $action, $location, $flag,NULL,$interface);          
            $soapArray = array('SOAP-ENV:', 'SOAP:');
            $cleanXml = str_ireplace($soapArray, '', $result);
            $xml = simplexml_load_string($cleanXml);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $xml;
    }
    
    public function getInventoryAcumaticaResponse($XMLGetRequest,$loginUrl, $action, $storeId = 0, $branch = NULL,$interface = NULL)
    {
        $xml = '';
        $cookies = array();
        try {
            //if (!is_object($this->client) || $branch != NULL || $interface == "Default") {
                $cookies = $this->login(array(), $loginUrl,$storeId);
                $this->client = $this->setClientCookie($loginUrl, $cookies);
            //}
            $location = str_replace('?wsdl', '', $loginUrl);
            $flag = '';
            $result = $this->client->__mySoapRequest($XMLGetRequest, $action, $location, $flag,NULL,1);
            $soapArray = array('SOAP-ENV:', 'SOAP:');
            $cleanXml = str_ireplace($soapArray, '', $result);
            $xml = simplexml_load_string($cleanXml);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $xml;
    }

    public function getAcumaticaWebserviceResponse($XMLGetRequest,$classAction,$storeId)
    {
        if($storeId==0){
            $scopeType = 'default';
        }else{
            $scopeType = 'stores';
        }
        $xml = '';
        try {
            $loginUrl = $this->urlHelper->getNewWebserviceUrl($scopeType,$storeId);
            if (!is_object($this->client)) {
                $cookies = $this->login(array(), $loginUrl,$storeId);
                $this->client = $this->setClientCookie($loginUrl, $cookies);
            }
            $location = str_replace('?wsdl', '', $loginUrl);
            $flag = '';
            $result = $this->client->__mySoapRequest($XMLGetRequest, $classAction, $location, $flag);
            $soapArray = array('SOAP-ENV:', 'SOAP:');
            $cleanXml = str_ireplace($soapArray, '', $result);
            $xml = simplexml_load_string($cleanXml);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $xml;
    }

    /**
     * @param $XMLGetRequest
     * @param $requestType
     * @param null $branch
     * @return \SimpleXMLElement|string
     */
    public function getAcumaticaResponseDefault($XMLGetRequest, $requestType,$storeId, $branch = NULL) // Here the default means service that we are using i.e KEMSConfig or default
    {
        $xml = '';
        $cookies = array();
        try {
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
            $loginUrl = $this->urlHelper->getBasicDefaultConfigUrl($serverUrl);
            if (!is_object($this->client) || $branch != NULL) {
                $cookies = $this->login(array(), $loginUrl,$storeId);
                $this->client = $this->setClientCookie($loginUrl, $cookies);
            }
            $location = str_replace('?wsdl', '', $loginUrl);
            $flag = '';
            $result = $this->client->__mySoapRequest($XMLGetRequest, '6.00.001/Default/' . $requestType, $location, $flag);
            $soapArray = array('SOAP-ENV:', 'SOAP:');
            $cleanXml = str_ireplace($soapArray, '', $result);
            $xml = simplexml_load_string($cleanXml);


        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $xml;
    }

    /**
     * @param $XMLGetRequest
     * @param $requestType
     * @return \SimpleXMLElement|string
     */
    public function getAcumaticaResponsePrice($XMLGetRequest, $requestType,$scopeType,$storeId)
    {
        $xml = '';
        try {
            $loginUrl = $this->urlHelper->getNewWebserviceUrl($scopeType,$storeId);
            if (!is_object($this->client)) {
                $cookies = $this->login(array(), $loginUrl,$storeId);
                $this->client = $this->setClientCookie($loginUrl, $cookies);
            }
            $location = str_replace('?wsdl', '', $loginUrl);
            $flag = '';
            $result = $this->client->__mySoapRequest($XMLGetRequest, $requestType, $location, $flag);
            $soapArray = array('SOAP-ENV:', 'SOAP:');
            $cleanXml = str_ireplace($soapArray, '', $result);
            $xml = simplexml_load_string($cleanXml);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $xml;
    }

    /**
     * @return string
     * Technical support email for error logs
     */
    public function logErrorSenderEmail()
    {
        $data = array(
            'name' => 'Technical Support',
            'email' => 'support@kensium.com'
        );
        return $data;
    }
    /**
     * For clear cache
     */
    public function clearCache()
    {
       $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
       foreach ($types as $type) {
           $this->_cacheTypeList->cleanType($type);
       }
       foreach ($this->_cacheFrontendPool as $cacheFrontend) {
           $cacheFrontend->getBackend()->clean();
       }
    }
    /**
     * Get Log file path from configuration
     */
    public function getLogPath()
    {
        /**
         * Path will take from common configuration if available
         * If not available then it takes static "/var/logs/" from root magento folder
         */
        $path = $this->syncResourceModel->getDataFromCoreConfig('amconnectorcommon/log_setting/default_logpath',NULL,NULL);
        if($path == ''){
            return $path = "/var/log/";
        }else{
            $path = rtrim($path, '/') . '/';
            return '/'.ltrim($path,'/');
        }
    }
    public function acumaticaSessionLogout($client)
    {
        try
        {
            $logout = $client->__soapCall('Logout', array());
        } catch(Exception $e)
        {
            echo $e->getMessage();
        }
    }

}
