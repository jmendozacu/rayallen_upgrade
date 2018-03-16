<?php
namespace Iglobal\Stores\Model\Payment;


class IglobalPaypal extends \Magento\OfflinePayments\Model\Checkmo
{

    protected $_code  = 'iGlobalPaypal';
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

