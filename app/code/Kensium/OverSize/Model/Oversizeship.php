<?php
/**
 * Kensium_OverSize extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Kensium
 *                     @package   Kensium_OverSize
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Kensium\OverSize\Model;

/**
 * @method Oversizeship setSku($sku)
 * @method Oversizeship setPrice($price)
 * @method mixed getSku()
 * @method mixed getPrice()
 * @method Oversizeship setCreatedAt(\string $createdAt)
 * @method string getCreatedAt()
 * @method Oversizeship setUpdatedAt(\string $updatedAt)
 * @method string getUpdatedAt()
 */
class Oversizeship extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     * 
     * @var string
     */
    const CACHE_TAG = 'kensium_oversize_oversizeship';

    /**
     * Cache tag
     * 
     * @var string
     */
    protected $_cacheTag = 'kensium_oversize_oversizeship';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'kensium_oversize_oversizeship';


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kensium\OverSize\Model\ResourceModel\Oversizeship');
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
