<?php
/**
 * Kensium_Contact extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_Contact
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\Contact\Model;

/**
 * @method Contact setFname($fname)
 * @method Contact setLname($lname)
 * @method Contact setAddress($address)
 * @method Contact setCity($city)
 * @method Contact setState($state)
 * @method Contact setZip($zip)
 * @method Contact setEmail($email)
 * @method Contact setPhone($phone)
 * @method Contact setFax($fax)
 * @method Contact setWebsite($website)
 * @method Contact setCompany($company)
 * @method Contact setPosition($position)
 * @method Contact setComments($comments)
 * @method Contact setOptOut($optOut)
 * @method mixed getFname()
 * @method mixed getLname()
 * @method mixed getAddress()
 * @method mixed getCity()
 * @method mixed getState()
 * @method mixed getZip()
 * @method mixed getEmail()
 * @method mixed getPhone()
 * @method mixed getFax()
 * @method mixed getWebsite()
 * @method mixed getCompany()
 * @method mixed getPosition()
 * @method mixed getComments()
 * @method mixed getOptOut()
 * @method Contact setCreatedAt(\string $createdAt)
 * @method string getCreatedAt()
 * @method Contact setUpdatedAt(\string $updatedAt)
 * @method string getUpdatedAt()
 */
class Contact extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     * 
     * @var string
     */
    const CACHE_TAG = 'kensium_contact_contact';

    /**
     * Cache tag
     * 
     * @var string
     */
    protected $_cacheTag = 'kensium_contact_contact';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'kensium_contact_contact';


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kensium\Contact\Model\ResourceModel\Contact');
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
