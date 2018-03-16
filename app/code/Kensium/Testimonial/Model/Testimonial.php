<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Enterprise testimonial model
 *
 * @method \Kensium\Testimonial\Model\ResourceModel\Testimonial _getResource()
 * @method \Kensium\Testimonial\Model\ResourceModel\Testimonial getResource()
 * @method string getName()
 * @method \Kensium\Testimonial\Model\Testimonial setName(string $value)
 * @method int getIsEnabled()
 * @method \Kensium\Testimonial\Model\Testimonial setIsEnabled(int $value)
 * @method \Kensium\Testimonial\Model\Testimonial setTypes(string $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Kensium\Testimonial\Model;

class Testimonial extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Representation value of enabled testimonial
     *
     */
    const STATUS_ENABLED = 1;

    /**
     * Representation value of disabled testimonial
     *
     */
    const STATUS_DISABLED = 0;

    /**
     * Representation value of disabled testimonial
     *
     */
    const CACHE_TAG = 'testimonial';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'kensium_testimonial';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getTestimonial() in this case
     *
     * @var string
     */
    protected $_eventObject = 'testimonial';

    /**
     * Store testimonial contents per store view
     *
     * @var array
     */
    protected $_contents = [];

    /**
     * Initialize testimonial model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kensium\Testimonial\Model\ResourceModel\Testimonial');
    }

    /**
     * Get all existing testimonial contents
     *
     * @return array|null
     */
    public function getStoreContents()
    {
        if (!$this->hasStoreContents()) {
            $contents = $this->_getResource()->getStoreContents($this->getId());
            $this->setStoreContents($contents);
        }
        return $this->_getData('store_contents');
    }


    /**
     * Save testimonial content, bind testimonial to catalog and sales rules after testimonial save
     *
     * @return $this
     */
    public function afterSave()
    {
        if ($this->hasStoreContents()) {
            $this->_getResource()->saveStoreContents(
                $this->getId(),
                $this->getStoreContents(),
                $this->getStoreContentsNotUse()
            );
        }
        if ($this->hasTestimonialCatalogRules()) {
            $this->_getResource()->saveCatalogRules($this->getId(), $this->getTestimonialCatalogRules());
        }
        if ($this->hasTestimonialSalesRules()) {
            $this->_getResource()->saveSalesRules($this->getId(), $this->getTestimonialSalesRules());
        }
        return parent::afterSave();
    }

    /**
     * Validate some data before saving
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        if ('' == trim($this->getName())) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a name.'));
        }
        return parent::beforeSave();
    }

    /**
     * Collect store ids in which current testimonial has content
     *
     * @return array|null
     */
    public function getStoreIds()
    {
        $contents = $this->getStoreContents();
        if (!$this->hasStoreIds()) {
            $this->setStoreIds(array_keys($contents));
        }
        return $this->_getData('store_ids');
    }

    /**
     * Make types getter always return array
     *
     * @return array
     */
    public function getTypes()
    {
        $types = $this->_getData('types');
        if (is_array($types)) {
            return $types;
        }
        if (empty($types)) {
            $types = [];
        } else {
            $types = explode(',', $types);
        }
        $this->setData('types', $types);
        return $types;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     *
     *
     */
    public function getTestimonialsCollection()
    {
        return $this->_getResource()->getTestimonialCollection();
    }
}
