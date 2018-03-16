<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Kensium\Tablerate\Plugin\Payment\Method\Checkmo;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Backend\Model\Auth\Session as BackendSession;
use Magento\OfflinePayments\Model\Checkmo;

class Available
{

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @param CustomerSession $customerSession
     * @param BackendSession $backendSession
     */
    public function __construct(
        CustomerSession $customerSession,
        BackendSession $backendSession
    ) {
        $this->customerSession = $customerSession;
        $this->backendSession = $backendSession;
    }

    /**
     *
     * @param Cashondelivery $subject
     * @param $result
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterIsAvailable(Checkmo $subject, $result)
    {
        // Do not remove payment method for admin
        if ($this->backendSession->isLoggedIn()) {
            return $result;
        }else {
           return false;
        }

        $isLogged = $this->customerSession->isLoggedIn();
        if (!$isLogged) {
            return false;
        }

        return $result;
    }
}
