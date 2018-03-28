<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kensium\QuickOrder\Model;

use Magento\Customer\Model;

/**
 * Customer session model
 * @method string getNoReferer()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Session extends \Magento\Customer\Model\Session
{
    /**
     * Authenticate controller action by login customer
     *
     * @param   bool|null $loginUrl
     * @return  bool
     */
    public function authenticate($loginUrl = null)
    {
        $currentUrl = $this->_createUrl()->getUrl('*/*/*', ['_current' => true]);
        if(strpos($currentUrl,"uploadFile") > 0){
           return true;
        }
        if ($this->isLoggedIn()) {
            return true;
        }
        $this->setBeforeAuthUrl($this->_createUrl()->getUrl('*/*/*', ['_current' => true]));
        if (isset($loginUrl)) {
            $this->response->setRedirect($loginUrl);
        } else {
            $arguments = $this->_customerUrl->getLoginUrlParams();
            if ($this->_session->getCookieShouldBeReceived() && $this->_createUrl()->getUseSession()) {
                $arguments += [
                    '_query' => [
                        $this->sidResolver->getSessionIdQueryParam($this->_session) => $this->_session->getSessionId(),
                    ]
                ];
            }
            $this->response->setRedirect(
                $this->_createUrl()->getUrl(\Magento\Customer\Model\Url::ROUTE_ACCOUNT_LOGIN, $arguments)
            );
        }

        return false;
    }

}
