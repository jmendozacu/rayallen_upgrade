<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Amasty\Promo\Block\Customer;

use Symfony\Component\Config\Definition\Exception\Exception;
use Magento\Customer\Model\Customer;

class AccountLink extends \Magento\Framework\View\Element\Template
{

    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $gigyahelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $_coreRegistry
     * @param Vendor $vendor
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $_coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    )
    {
        $this->_coreRegistry = $_coreRegistry;
        $this->_filterProvider = $filterProvider;
        //$this->gigyahelper = $gigyahelper;
        $this->_customerSession = $customerSession;
        $this->customerFactory  = $customerFactory;
        parent::__construct($context, $data);
    }

    /*
     * It will return array of vendor names
     * @return array
     */
    public function getBalanceRewardPoints()
    {
        $_customer = $this->_customerSession->getCustomer();

        $websiteId  = $this->_storeManager->getWebsite()->getWebsiteId();

        // Instantiate object (this is the most important part)
        $customer   = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->load($_customer->getId());
        $params = array(
            'UID' => ""//$customer->getGigyaUid()
        );
        $totalPoints = 0;
        /*if($customer->getGigyaUid()) {
            try {
                $gigyResponse = $this->gigyahelper->_gigya_api('gm.getChallengeStatus', $params);
                if(!empty($gigyResponse['achievements'])){              
                foreach($gigyResponse['achievements'] as $achieve) {
                    $totalPoints += $achieve['pointsTotal'];
                }
                }
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }*/
        return $totalPoints;
    }

    /**
     * Get current store name.
     *
     * @return string
     */
    public function getCurrentStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }
    /**
     * Get current store id.
     *
     * @return string
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
?>
