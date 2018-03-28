<?php
/**
 * Kensium_Catalogrequest extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_Catalogrequest
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\Catalogrequest\Model;

/**
 * @method Catalogrequest setFname($fname)
 * @method Catalogrequest setLname($lname)
 * @method Catalogrequest setStoreId($store_id)
 * @method Catalogrequest setTitle($title)
 * @method Catalogrequest setBusinessname($businessname)
 * @method Catalogrequest setAddress($address)
 * @method Catalogrequest setCity($city)
 * @method Catalogrequest setState($state)
 * @method Catalogrequest setZip($zip)
 * @method Catalogrequest setCountry($country)
 * @method Catalogrequest setEmail($email)
 * @method Catalogrequest setPhone($phone)
 * @method Catalogrequest setHearing($hearing)
 * @method Catalogrequest setOptOut($optOut)
 * @method mixed getFname()
 * @method mixed getLname()
 * @method mixed getTitle()
 * @method mixed getBusinessname()
 * @method mixed getAddress()
 * @method mixed getCity()
 * @method mixed getState()
 * @method mixed getZip()
 * @method mixed getCountry()
 * @method mixed getEmail()
 * @method mixed getPhone()
 * @method mixed getHearing()
 * @method mixed getOptOut()
 * @method Catalogrequest setCreatedAt(\string $createdAt)
 * @method string getCreatedAt()
 * @method Catalogrequest setUpdatedAt(\string $updatedAt)
 * @method string getUpdatedAt()
 */
class Catalogrequest extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     * 
     * @var string
     */
    const CACHE_TAG = 'kensium_catalogrequest_catalogrequest';

    /**
     * Cache tag
     * 
     * @var string
     */
    protected $_cacheTag = 'kensium_catalogrequest_catalogrequest';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'kensium_catalogrequest_catalogrequest';


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kensium\Catalogrequest\Model\ResourceModel\Catalogrequest');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
