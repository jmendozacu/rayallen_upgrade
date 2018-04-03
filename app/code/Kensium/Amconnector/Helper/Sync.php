<?php
/**
 *
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
class Sync extends AbstractHelper
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var
     */
    protected $baseDirPath;

    /**
     * @param Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Magento\Framework\App\Filesystem\DirectoryList $baseDirPath
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Request\Http $request,
        StoreManagerInterface $storeRepository,
        \Magento\Framework\App\Filesystem\DirectoryList $baseDirPath
    )
    {
        parent::__construct($context);
        $this->request = $request;
        $this->storeRepository = $storeRepository;
        $this->baseDirPath = $baseDirPath;
    }


    /**
     * @param null $storeCode
     * @return int
     */
    public function getCurrentStoreId($storeCode = null)
    {
        /* if ($storeCode == null)
             $storeCode = $this->request->getParam('store');
         $storeId = 0;
         if ($storeCode) {
             $storeId = $this->storeRepository->get($storeCode)->getId();
         }*/

        $storeId = $this->request->getParam('store');
        return $storeId;
    }

    /**
     * @param null $entityCode
     * @return null
     */
    public function getEnvelopeData($entityCode=NULL)
    {
        $baseRootPath = $this->baseDirPath->getRoot();
        $csvEnvelopeFile = $baseRootPath.'/apiclient/acumatica_envelops.csv';
        $handle = fopen($csvEnvelopeFile, "r");
        $i = 0;
        $requiredData = array();
        while (($data[] = fgetcsv($handle, 10000, "|")) !== FALSE) {
            if ($i < 1 || $data['0'] == '') {
                $i++;
            } else {
                $envCode = strtolower(trim($data[$i][1]));
                if($envCode == strtolower(trim($entityCode))){
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
     * Number of record sync in trail license
     */
    public function numberOfRecordSyncInTrialLicense()
    {
        $trialRecord = '10000';
        return $trialRecord;
    }

}
