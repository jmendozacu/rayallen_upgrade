<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\System\Config;

use Magento\Framework\Controller\Result\JsonFactory;

class TimeSetting extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Kensium\Amconnector\Helper\Sync
     */
    protected $syncHelper;

    /**
     * @var \Kensium\Amconnector\Model\VerifyTimeSetting
     */
    protected $verifyTimeSetting;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Kensium\Amconnector\Helper\Sync $syncHelper
     * @param \Kensium\Amconnector\Model\VerifyTimeSetting $verifyTimeSetting
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory,
        \Kensium\Amconnector\Helper\Sync $syncHelper,
       \Kensium\Amconnector\Model\VerifyTimeSetting $verifyTimeSetting
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->syncHelper=$syncHelper;
        $this->verifyTimeSetting=$verifyTimeSetting;
    }

    /**
     * @return mixed
     */
    protected function _check()
    {
        $scope = $this->getRequest()->getParam('scope');
        $autoTimeSync = $this->getRequest()->getParam('autoTimeSync');
        if ($scope == "default") {
            $scopeType = $scope;
            $scopeId = 0;
        } elseif ($scope == "stores") {
            $scopeType = $scope;
            $scopeId = $this->getRequest()->getParam('storeId');
        } else {
            $scopeType = "websites";
            $scopeId = $this->syncHelper->getCurrentStoreId($scopeType);
        }

        return $this->verifyTimeSetting->verifyTimeSetting($autoTimeSync,$scopeType,$scopeId);
    }

    /**
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->_check();
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        if ($result == 0) {
            $resultJson->setData([
                'valid' => 0,
                'message' => 'Time Setting Checking Failed',
            ]);
        } else {
            return $resultJson->setData([
                'valid' => 1,
                'message' => 'Time Setting checked successfully.',
                'result' => $result
            ]);
        }
    }
}
