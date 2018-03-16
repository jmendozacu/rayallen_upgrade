<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\System\Config\Source;

use Magento\Store\Model\StoreManagerInterface;

class DomainNames
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    )
    {
        $this->_storeManager = $storeManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $allStores = $this->_storeManager->getStores();
        $data = [];
        foreach ($allStores as $store) {
            $data[] = array('value' => $store->getBaseUrl('web').'store/'.$store->getName().'/storeview/'. $store->getCode(), 'label' => $store->getBaseUrl('web').'store/'.$store->getName().'/storeview/'.$store->getCode());
        }
        return $data;

    }
}
