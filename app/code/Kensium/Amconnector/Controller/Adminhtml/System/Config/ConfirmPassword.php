<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Controller\Adminhtml\System\Config;

class ConfirmPassword extends \Magento\Backend\App\Action
{
    /**
     * @return mixed
     */
    protected function _check()
    {
        $password = $this->getRequest()->getParam('password');
        $confirmPassword = $this->getRequest()->getParam('cPassword');
        if($password == $confirmPassword){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        echo $result = $this->_check();
    }
}
