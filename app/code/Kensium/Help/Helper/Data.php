<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Help\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Kensium\Amconnector\Helper\Url
     */
    protected $urlHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var \Kensium\Amconnector\Helper\Sync
     */
    protected $syncHelper;

    /**
     * @var \Kensium\Amconnector\Helper\Client
     */
    protected $clientHelper;

    const Version = '2.1';

    /**
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Kensium\Amconnector\Helper\Url $urlHelper
     * @param \Kensium\Amconnector\Helper\Sync $syncHelper
     * @param \Kensium\Amconnector\Helper\Client $clientHelper
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Kensium\Amconnector\Helper\Url $urlHelper,
        \Kensium\Amconnector\Helper\Sync $syncHelper,
        \Kensium\Amconnector\Helper\Client $clientHelper
    )
    {
        $this->context = $context;
        $this->storeManager = $storeManager;
        $this->urlHelper = $urlHelper;
        $this->syncHelper = $syncHelper;
        $this->clientHelper = $clientHelper;
        $this->messageManager = $messageManager;
        $this->scopeConfigInterface = $context->getScopeConfig();
    }

    /**
     * @return array
     */
    public function getRequirementsInfo()
    {
        $clientPhpData = $this->getPhpSettings();
        $acumaticaVersion = $this->getAcumaticaVersion();
        if($acumaticaVersion)
        {
            $versionSign = ">=";
        }else{
            $versionSign = "";
        }
        $requirements = array (

            'php_version' => array(
                'title' => 'PHP Version',
                'condition' => array(
                    'sign' => '>=',
                    'value' => '5.3.0'
                ),
                'current' => array(
                    'value' => $this->getPhpVersion(),
                    'status' => true
                )
            ),

            'memory_limit' => array(
                'title' => 'Memory Limit',
                'condition' => array(
                    'sign' => '>=',
                    'value' => '512 MB'
                ),
                'current' => array(
                    'value' => (int)$clientPhpData['memory_limit'] . ' MB',
                    'status' => true
                )
            ),

            'magento_version' => array(
                'title' => 'Magento Version',
                'condition' => array(
                    'sign' => '>=',
                    'value' => '2.1',
                ),
                'current' => array(
                    'value' => $this->getVersion(false),
                    'status' => true
                )
            ),

            'max_execution_time' => array(
                'title' => 'Max Execution Time',
                'condition' => array(
                    'sign' => '>=',
                    'value' => '360 sec'
                ),
                'current' => array(
                    'value' => (int)$clientPhpData['max_execution_time'] . ' sec',
                    'status' => true
                )
            ),
            'amconnector_version' => array(
                'title' => 'AM Connector Version',
                'condition' => array(
                    'sign' => '>=',
                    'value' => '2.0'
                ),
                'current' => array(
                    'value' =>'2.1',
                    'status' => true
                )
            ),
            'acumatica_instance' => array(
                'title' => 'Acumatica Version',
                'condition' => array(
                    'sign' => $versionSign,
                    'value' => $acumaticaVersion
                ),
                'current' => array(
                    'value' => $acumaticaVersion,
                    'status' => true
                )
            )
        );

        foreach ($requirements as $key => &$requirement) {

            // max execution time is unlimited
            if ($key == 'max_execution_time' && $clientPhpData['max_execution_time'] == 0) {
                continue;
            }

            $requirement['current']['status'] = version_compare(
                $requirement['current']['value'],
                $requirement['condition']['value'],
                $requirement['condition']['sign']
            );
        }

        return $requirements;
    }

    /**
     * @return array
     */
    public function getPhpSettings()
    {
        return array(
            'memory_limit' => $this->getMemoryLimit(),
            'max_execution_time' => @ini_get('max_execution_time'),
            'phpinfo' => $this->getPhpInfoArray()
        );
    }

    /**
     * @return array|mixed
     */
    public function getPhpInfoArray()
    {
        try {

            ob_start(); phpinfo(INFO_ALL);

            $pi = preg_replace(
                array(
                    '#^.*<body>(.*)</body>.*$#m', '#<h2>PHP License</h2>.*$#ms',
                    '#<h1>Configuration</h1>#',  "#\r?\n#", "#</(h1|h2|h3|tr)>#", '# +<#',
                    "#[ \t]+#", '#&nbsp;#', '#  +#', '# class=".*?"#', '%&#039;%',
                    '#<tr>(?:.*?)" src="(?:.*?)=(.*?)" alt="PHP Logo" /></a><h1>PHP Version (.*?)</h1>(?:\n+?)</td></tr>#',
                    '#<h1><a href="(?:.*?)\?=(.*?)">PHP Credits</a></h1>#',
                    '#<tr>(?:.*?)" src="(?:.*?)=(.*?)"(?:.*?)Zend Engine (.*?),(?:.*?)</tr>#',
                    "# +#", '#<tr>#', '#</tr>#'),
                array(
                    '$1', '', '', '', '</$1>' . "\n", '<', ' ', ' ', ' ', '', ' ',
                    '<h2>PHP Configuration</h2>'."\n".'<tr><td>PHP Version</td><td>$2</td></tr>'.
                    "\n".'<tr><td>PHP Egg</td><td>$1</td></tr>',
                    '<tr><td>PHP Credits Egg</td><td>$1</td></tr>',
                    '<tr><td>Zend Engine</td><td>$2</td></tr>' . "\n" .
                    '<tr><td>Zend Egg</td><td>$1</td></tr>', ' ', '%S%', '%E%'
                ), ob_get_clean()
            );

            $sections = explode('<h2>', strip_tags($pi, '<h2><th><td>'));
            unset($sections[0]);

            $pi = array();
            foreach ($sections as $section) {
                $n = substr($section, 0, strpos($section, '</h2>'));
                preg_match_all(
                    '#%S%(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?%E%#',
                    $section,
                    $askapache,
                    PREG_SET_ORDER
                );
                foreach ($askapache as $m) {
                    if (!isset($m[0]) || !isset($m[1]) || !isset($m[2])) {
                        continue;
                    }
                    $pi[$n][$m[1]]=(!isset($m[3])||$m[2]==$m[3])?$m[2]:array_slice($m,2);
                }
            }

        } catch (Exception $exception) {
            return array();
        }

        return $pi;
    }

    /**
     * @param bool $inMegabytes
     * @return int|string
     */
    public function getMemoryLimit($inMegabytes = true)
    {
        $memoryLimit = trim(ini_get('memory_limit'));

        if ($memoryLimit == '') {
            return 0;
        }

        $lastMemoryLimitLetter = strtolower(substr($memoryLimit, -1));
        switch($lastMemoryLimitLetter) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }

        if ($inMegabytes) {
            $memoryLimit /= 1024 * 1024;
        }

        return $memoryLimit;
    }

    /**
     * @return string
     */
    public function getPhpVersion()
    {
        $phpVersion = @phpversion();
        $array = explode("-",$phpVersion);
        return $array[0];
    }

    /**
     * @return string
     */
    public function getPhpApiName()
    {
        return @php_sapi_name();
    }

    /**
     * @param bool $asArray
     * @return array|string
     */
    public function getVersion($asArray = false)
    {
        $versionString = self::Version;//\Magento\Framework\AppInterface::VERSION;
        return $asArray ? explode('.',$versionString) : $versionString;
    }

    /**
     * @return string
     */
    public function getRevision()
    {
        return 'undefined';
    }

    /**
     * @return bool
     */
    public function isGoEdition()
    {
        return class_exists('Saas_Db',false);
    }


    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        return str_replace('index.php/','',$baseUrl);
    }

    /**
     * @return string
     * This function is used for getting acumatica version
     */
    public function getAcumaticaVersion()
    {
        $serverUrl = $this->scopeConfigInterface->getValue('amconnectorcommon/amconnectoracucon/serverUrl');
        if($serverUrl) {
            $url = $this->urlHelper->getBasicConfigUrl($serverUrl);
            try {
                $csvAcumaticaVerion = $this->syncHelper->getEnvelopeData('GETENDPOINTVERSION');

                $XMLGetRequest = $csvAcumaticaVerion['envelope'];
                $action = $csvAcumaticaVerion['envName'] . "/" . $csvAcumaticaVerion['envVersion'] . "/" . $csvAcumaticaVerion['methodName'];
                $getAcumaticaVerion = $this->clientHelper->getAcumaticaResponse($XMLGetRequest, $url, $action);

                $gateVersion = $getAcumaticaVerion->Body->GetResponse->GetResult->GateVersion->Value;
                if ($gateVersion != '') {
                    return $gateVersion;
                } else {
                    return '';
                }
            } catch (SoapFault $e) {
                echo "Last request:<pre>" . $e->getMessage() . "</pre>";
            }
        }else{

            return '';
        }
    }

}
