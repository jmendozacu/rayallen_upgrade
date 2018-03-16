<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Help\Controller\Adminhtml\Support;

use Magento\Backend\App\Action\Context;

class SupportEmails extends \Magento\Backend\App\Action
{

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $supportType = $this->getRequest()->getParam('supportType',NULL);
        if($supportType != '') {
            $supportArray = array('1' => 'sales@kensium.com', '2' => 'support@kensium.com', '3' => 'sales@kensium.com', '4' => 'support@kensium.com', '5' => 'support@kensium.com', '6' => '911@kensium.com', '7' => 'support@kensium.com', '8' => 'support@kensium.com', '9' => 'support@kensium.com');
            echo $supportArray[$supportType];
        }else{
            echo '';
        }
    }
}
