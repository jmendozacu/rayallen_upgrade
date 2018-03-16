<?php

/**
 * Copyright ©  Inc. All rights reserved.
 *
 */

namespace Kensium\Lib;

use Kensium\Lib;


class Common {

    /**
     * @var
     */
    protected $client;

    /**
     * @param null $entityCode
     * @return null
     */
    public function getEnvelopeData($entityCode = NULL) {

        $csvEnvelopeFile = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'apiclient/acumatica_envelops.csv';
        $handle = fopen($csvEnvelopeFile, "r");
        $i = 0;
        $requiredData = array();
        while (($data[] = fgetcsv($handle, 10000, "|")) !== FALSE) {
            if ($i < 1 || $data['0'] == '') {
                $i++;
            } else {
                $envCode = strtolower(trim($data[$i][1]));
                if ($envCode == strtolower(trim($entityCode))) {
                    $requiredData['envVersion'] = $data[$i][4];
                    $requiredData['envName'] = $data[$i][5];
                    $requiredData['methodName'] = $data[$i][6];
                    $requiredData['description'] = $data[$i][7];
                    $requiredData['envelope'] = $data[$i][8];
                    return $requiredData;
                }
                $i++;
            }
        }
        return null;
    }

    /**
     * @param $serverUrl
     * @return string
     */
    public function getBasicConfigUrl($serverUrl) {
        return $serverUrl . 'entity/KemsConfig/6.00.001?wsdl';
    }

    /**
     * @param $serverUrl
     * @return string
     */
    public function getBasicDefaultConfigUrl($serverUrl) {
        return $serverUrl . 'entity/Default/6.00.001?wsdl';
    }

    /**
     * @param $serverUrl
     * @return string
     */
    public function getBasicConfigNormalUrl($serverUrl) {
        return $serverUrl . 'entity/KemsConfig/6.00.001.asmx';
    }

    public function getAcumaticaResponse($configParameters,$XMLGetRequest,$loginUrl, $action,  $branch = NULL,$interface = NULL)
    {
        $xml = '';
        $cookies = array();
        try {
            $location = str_replace('?wsdl', '', $loginUrl);
            if (strpos($loginUrl,'Soap') == false) {
                if (strpos($loginUrl,'company') == false) {
                    $company = $configParameters['company'];
                    $newCompany = str_replace(" ","%20",$company);
                    if($newCompany != '')
                        $location .= '?company='.$newCompany;
                }
            }
            if(strstr($location,'&comapny='))
                $location = str_replace('&comapny', '?company', $loginUrl);
            /* if (!is_object($this->client) || $branch != NULL || $interface != NULL) {*/
            $cookies = $this->login(array(),$configParameters, $loginUrl,$branch);
            $this->client = $this->setClientCookie($loginUrl, $cookies,$configParameters['company']);

            $action = 'http://www.acumatica.com/entity/'.$action;
            if(strstr($action,'.asmx'))
                $action = 'http://www.acumatica.com/typed/'.$action;
                
            $result = $this->client->__doRequest($XMLGetRequest, $location, $action, $version = 1);
            $this->acumaticaSessionLogout($this->client);

            $soapArray = array('SOAP-ENV:', 'SOAP:');
            $cleanXml = str_ireplace($soapArray, '', $result);
            $xml = simplexml_load_string($cleanXml);
			//echo "HERE:::=====>>>>> ".$xml. " <br \>";
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $xml;
    }

    /**
     * @param array $params
     * @param $serverUrl
     * @param null $branch
     * @return array|int
     */
    public function login($params = array(), $configParameters, $serverUrl, $branch = NULL) {

        $response['message'] = 'ERROR in soap call';
        try {

            if (empty($params)) {
                $request = $configParameters;
            } else {
                $request = $params;
            }
            if (isset($request['company'])) {
                $company = $request['company'];
            }
            //Customization//
            if(isset($branch)&& !empty($branch)) {
				$request['branch'] = $branch;
			}
			//Customization//
            $url = $serverUrl;

            if (strpos($url, 'company') == false) {
                $newCompany = str_replace(" ", "%20", $company);
                $url .= '&company=' . $newCompany;
            }

            /* TODO need to log this */
            /* if (!extension_loaded('soap')) {
              $this->logger->critical(new \Magento\Framework\Exception\LocalizedException(__('PHP SOAP extension is required.')));
              return $response;
              } */

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
     * @return soapclient
     */
    public function setClientCookie($url, $cookies, $company) {
        try {
            if (strpos($url, 'company') == false) {

                $newCompany = str_replace(" ", "%20", $company);
                $url .= '&company=' . $newCompany;
            }
            $client = new \SoapClient($url, array(
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
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Logout call
     */
    public function acumaticaSessionLogout($client) {
        try {
            $logout = $client->__soapCall('Logout', array());
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Number of record sync in trail license
     */
    public function numberOfRecordSyncInTrialLicense() {
        $trialRecord = '10000';
        return $trialRecord;
    }

    public function callAmconnectorLicenseServer($licenseKey) {
        $url = 'http://licensing.kensium.com/service.asmx?WSDL';
        $client = new \SoapClient($url, array("trace" => 1, "exception" => 0));
        $arguments = array(
            'SerialCode' => $licenseKey,
            'ProductCode' => 'KEMSLIC20'
        );
        $result = $client->__call('ValidateSerialNumber', array($arguments));
        $response = $result->ValidateSerialNumberResult;
        return $response;
    }

    /**
     * @param $xmlObject
     * @param array $out
     * @return array
     */
    public function xml2array($xmlObject, $out = array())
    {
        foreach ((array)$xmlObject as $index => $node)
            $out[$index] = (is_object($node)) ? $this->xml2array($node) : $node;

        return $out;
    }


    public function getShipmentDetails($shipmentNbr, $storeId,$configParameters,$url) {
        $XMLRequest = '';
        $envelopeData = $this->getEnvelopeData('GETSHIPMENTBYNUMBER');
        $XMLRequest = $envelopeData['envelope'];
        //Send Shipment request to Acumatica
        $XMLRequest = str_replace('{{SHIPMENTNUMBER}}', $shipmentNbr, $XMLRequest);
        /*End Soap Request parmeters*/
	$endpointUrl = $url ."entity/".$envelopeData['envName'] . "/" . $envelopeData['envVersion'] . "?wsdl";
        $location = str_replace('?wsdl', '', $endpointUrl);
        $requestString = $envelopeData['envName'] . '/' . $envelopeData['envVersion'] . '/' . $envelopeData['methodName'];
        //Send  request to Acumatica
        $flag = '';
        $response = $this->getAcumaticaResponse($configParameters, $XMLRequest, $endpointUrl, $requestString);

        $xml = $response;
        $data =  $totalData = '';
        if(isset($xml->Body->GetListResponse->GetListResult)) {
            $data = $xml->Body->GetListResponse->GetListResult;
            $totalData = $this->xml2array($data);
        }
        $shipmentResults = array();
        if (isset($totalData['Entity']))
            $shipmentResults = $totalData['Entity'];
        $i = 0;
        $shipments = array();
        $oneShipmentRecordFlag = false;
        /**
         * If we have multi Shipment and multi or single tracking numbers
         */
        foreach ($shipmentResults['Details']['ShipmentDetail'] as $shipKey => $shipResult) {
            if (!is_numeric($shipKey)) {
                $oneShipmentRecordFlag = true;
                break;
            }
            $shipResult = $this->xml2array($shipResult);
            if (isset($shipResult['InventoryID']['Value']) && $shipResult['InventoryID']['Value'] != '') {
                $shipments[$i]['InventoryId'] = $shipResult['InventoryID']['Value'];
                if (isset($shipResult['ShippedQty']['Value']))
                    $shipments[$i]['ShippedQty'] = $shipResult['ShippedQty']['Value'];
                $shipments[$i]['OrderNbr'] = $shipResult['OrderNbr']['Value'];

                $oneTrackingNbrFlg = false;
		if(isset($shipmentResults['Packages']['ShipmentPackage'])) {
                foreach ($shipmentResults['Packages']['ShipmentPackage'] as $trackKey => $track) {
                    if (!is_numeric($trackKey)) {
                        $oneTrackingNbrFlg = true;
                        break;
                    }
                    $track = $this->xml2array($track);

                    $shipments[$i]['ShippedQty'] = $shipResult['ShippedQty']['Value'];
                    $shipments[$i]['OrderNbr'] = $shipResult['OrderNbr']['Value'];
                    $shipments[$i]['InventoryId'] = $shipResult['InventoryID']['Value'];
                    if (isset($track['TrackingNumber']['Value']) && $track['TrackingNumber']['Value'] != '') {
                        $shipments[$i]['TrackingNumber'] = $track['TrackingNumber']['Value'];
                    } else {
                        $shipments[$i]['TrackingNumber'] = '';
                    }
                    $i++;
                } }
                if ($oneTrackingNbrFlg) {
                    if (isset($shipmentResults['Packages']['ShipmentPackage']['TrackingNumber']['Value']) && $shipmentResults['Packages']['ShipmentPackage']['TrackingNumber']['Value'] != '') {
                        $shipments[$i]['TrackingNumber'] = $shipmentResults['Packages']['ShipmentPackage']['TrackingNumber']['Value'];
                    } else {
                        $shipments[$i]['TrackingNumber'] = '';
                    }
                    $i++;
                }
            }
        }
        /**
         * If we have Single Shipment and multi or single tracking numbers
         */
        if ($oneShipmentRecordFlag) {
            if (isset($shipmentResults['Details']['ShipmentDetail']['InventoryID']['Value']) && $shipmentResults['Details']['ShipmentDetail']['InventoryID']['Value'] != '') {
                $shipments[$i]['InventoryId'] = $shipmentResults['Details']['ShipmentDetail']['InventoryID']['Value'];
                if (isset($shipmentResults['Details']['ShipmentDetail']['ShippedQty']['Value']))
                    $shipments[$i]['ShippedQty'] = $shipmentResults['Details']['ShipmentDetail']['ShippedQty']['Value'];

                $shipments[$i]['OrderNbr'] = $shipmentResults['Details']['ShipmentDetail']['OrderNbr']['Value'];

                $oneTrackingNbrFlg = false;
		if(isset($shipmentResults['Packages']['ShipmentPackage'])) {
                foreach ($shipmentResults['Packages']['ShipmentPackage'] as $trackKey => $track) {
                    if (!is_numeric($trackKey)) {
                        $oneTrackingNbrFlg = true;
                        break;
                    }
                    $track = $this->xml2array($track);

                    $shipments[$i]['ShippedQty'] = $shipmentResults['Details']['ShipmentDetail']['ShippedQty']['Value'];
                    $shipments[$i]['OrderNbr'] = $shipmentResults['Details']['ShipmentDetail']['OrderNbr']['Value'];
                    $shipments[$i]['InventoryId'] = $shipmentResults['Details']['ShipmentDetail']['InventoryID']['Value'];
                    if (isset($track['TrackingNumber']['Value']) && $track['TrackingNumber']['Value'] != '') {
                        $shipments[$i]['TrackingNumber'] = $track['TrackingNumber']['Value'];
                    } else {
                        $shipments[$i]['TrackingNumber'] = '';
                    }
                    $i++;
                }
                if ($oneTrackingNbrFlg) {
                    if (isset($shipmentResults['Packages']['ShipmentPackage']['TrackingNumber']['Value']) && $shipmentResults['Packages']['ShipmentPackage']['TrackingNumber']['Value'] != '') {
                        $shipments[$i]['TrackingNumber'] = $shipmentResults['Packages']['ShipmentPackage']['TrackingNumber']['Value'];
                    } else {
                        $shipments[$i]['TrackingNumber'] = '';
                    }
                }}
            }
        }
        return $shipments;
    }

}

//use Magento\Framework\Css\PreProcessor\File\Collector\Library;
