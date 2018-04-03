<?php

/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2017 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Amconnector\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;


class AjaxLog extends Action
{

    public function __construct(
    Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute() {
        $logPath = $this->getRequest()->getParam('logPath', NULL);
        $result = $this->resultJsonFactory->create();

        try {
            if(!empty($logPath)){
                $my_file = '';
                $file_handle = fopen($logPath, "r");
                while (!feof($file_handle)) {
                   $my_file .= fgetss($file_handle);
                }
                fclose($file_handle);
                $logdata['resultdata'] = $my_file;
            } else {
                $logdata['resultdata'] = __('Log path not set. please set logpath in phtml file.');
            }
        } catch (\Exception $e) {
            $logdata['resultdata'] = $e->getMessage();
        }
        return $result->setData($logdata);
    }

}
