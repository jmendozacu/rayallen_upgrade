<?php
namespace Iglobal\Stores\Model\Payment;


class Iglobal extends \Magento\OfflinePayments\Model\Checkmo
{

    protected $_code  = 'iGlobal';
    protected $_canUseInternal = true;
    protected $_canUseCheckout = false;
    protected $_canUseForMultishipping = false;

    public function getPayableTo()
    {
        return false;
    }

    public function getMailingAddress()
    {
        return false;
    }
}