<?php
/**
 * Kensium_Quote extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_Quote
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\Quote\Model;

/**
 * @method Quote setFname($fname)
 * @method Quote setLname($lname)
 * @method Quote setStoreId($store_id)
 * @method Quote setBname($bname)
 * @method Quote setAddress($address)
 * @method Quote setCity($city)
 * @method Quote setState($state)
 * @method Quote setZip($zip)
 * @method Quote setCountry($country)
 * @method Quote setPhone($phone)
 * @method Quote setFax($fax)
 * @method Quote setEmail($email)
 * @method Quote setQuantity($quantity)
 * @method Quote setItem($item)
 * @method Quote setDescription($description)
 * @method mixed getFname()
 * @method mixed getLname()
 * @method mixed getBname()
 * @method mixed getAddress()
 * @method mixed getCity()
 * @method mixed getState()
 * @method mixed getZip()
 * @method mixed getCountry()
 * @method mixed getPhone()
 * @method mixed getFax()
 * @method mixed getEmail()
 * @method mixed getQuantity()
 * @method mixed getItem()
 * @method mixed getDescription()
 * @method Quote setCreatedAt(\string $createdAt)
 * @method string getCreatedAt()
 * @method Quote setUpdatedAt(\string $updatedAt)
 * @method string getUpdatedAt()
 */
class Quote extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     * 
     * @var string
     */
    const CACHE_TAG = 'kensium_quote_quote';

    /**
     * Cache tag
     * 
     * @var string
     */
    protected $_cacheTag = 'kensium_quote_quote';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'kensium_quote_quote';


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kensium\Quote\Model\ResourceModel\Quote');
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
