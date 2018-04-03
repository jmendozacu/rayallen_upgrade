<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Helper;

use SoapClient;

/**
 * Extend SoapClientClass
 */
class AmconnectorSoap extends SoapClient
{
    /**
     * @param mixed $wsdl
     * @param array $options
     */
    function __construct($wsdl, $options)
    {
        parent::__construct($wsdl, $options);
    }

    /**
     * @param $request
     * @param $location
     * @param $action
     * @param $version
     * @return string
     */
    public function __doSoapRequest($request, $location, $action, $version)
    {
        $result = parent::__doRequest($request, $location, $action, $version);
        return $result;
    }

    /**
     * @param $array
     * @param $op
     * @param $location
     * @param $flag
     * @return string
     */
    function __mySoapRequest($request, $op, $location,$flag, $entityFlag = NULL,$interface = NULL)
    {
        if($flag == 'webservice')
            $entity = 'typed';
        else
            $entity = 'entity';

        if($interface != NULL)
            $entity = 'generic';

        if ($entityFlag == NULL)
            $action = 'http://www.acumatica.com/'.$entity.'/' . $op;
        else
            $action = 'http://www.acumatica.com/'.$entity.'/6.00.001/KEMS/' . $op;

        $version = '1';
        $result = $this->__doSoapRequest($request, $location, $action, $version);
        return $result;
    }
}

