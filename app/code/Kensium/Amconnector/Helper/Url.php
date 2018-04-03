<?php
/**
 *
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;

class Url extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    const SCOPE_TYPE = 'stores';
    protected  $scopeConfigInterface;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
        $this->scopeConfigInterface = $context->getScopeConfig();
    }

    /**
     * @return string
     */
    public function getSchemaUrl($storeId=0)
    {
        $acumaticaUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',\Kensium\Amconnector\Helper\Url::SCOPE_TYPE,$storeId);
        if(isset($acumaticaUrl) && $acumaticaUrl != '')
        {
            $acumaticaSchemaUrl = $acumaticaUrl . 'entity/maintenance/5.31?wsdl';
            return $acumaticaSchemaUrl;
        }else{
            return NULL;
        }
    }


    /**
     * @return string
     */
    public function getCustomerLocationUrl($storeId)
    {
        $acumaticaUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl', \Kensium\Amconnector\Helper\Url::SCOPE_TYPE,$storeId);
        $acumaticaCustLocationUrl = $acumaticaUrl . 'entity/maintenance/5.31';
        return $acumaticaCustLocationUrl;
    }
    public function getProductLocationUrl($storeId)
    {
        $acumaticaUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',self::SCOPE_TYPE,$storeId);
        $acumaticaCustLocationUrl = $acumaticaUrl . 'entity/maintenance/5.31';
        return $acumaticaCustLocationUrl;
    }
    /**
     * @param $scopeType
     * @param $storeId
     * @return string
     * This web service url from acumatica
     */
    public function getNewWebserviceUrl($scopeType,$storeId)
    {
        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl',$scopeType,$storeId);
        if(!isset($serverUrl))
        {
            $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
        }
        $url = $serverUrl.'Soap/KEMS.asmx?wsdl';
        return $url;
    }

    /**
     * @param $serverUrl
     * @return string
     */
    public function getBasicConfigUrl($serverUrl)
    {
        return $serverUrl . 'entity/KemsConfig/6.00.001?wsdl';
    }

    /**
     * @param $serverUrl
     * @return string
     */
    public function getBasicDefaultConfigUrl($serverUrl)
    {
        return $serverUrl . 'entity/Default/6.00.001?wsdl';
    }

    /**
     * @param $serverUrl
     * @return string
     */
    public function getBasicConfigNormalUrl($serverUrl)
    {
        return $serverUrl . 'entity/KemsConfig/6.00.001.asmx';
    }
}