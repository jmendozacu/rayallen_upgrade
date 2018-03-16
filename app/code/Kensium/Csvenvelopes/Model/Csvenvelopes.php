<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

// @codingStandardsIgnoreFile

/**
 * Enterprise csvenvelopes model
 *
 * @method \Kensium\Csvenvelopes\Model\ResourceModel\Csvenvelopes _getResource()
 * @method \Kensium\Csvenvelopes\Model\ResourceModel\Csvenvelopes getResource()
 * @method string getName()
 * @method \Kensium\Csvenvelopes\Model\Csvenvelopes setName(string $value)
 * @method int getIsEnabled()
 * @method \Kensium\Csvenvelopes\Model\Csvenvelopes setIsEnabled(int $value)
 * @method \Kensium\Csvenvelopes\Model\Csvenvelopes setTypes(string $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Kensium\Csvenvelopes\Model;

class Csvenvelopes extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Representation value of enabled csvenvelopes
     *
     */
    const STATUS_ENABLED = 1;

    /**
     * Representation value of disabled csvenvelopes
     *
     */
    const STATUS_DISABLED = 0;

    /**
     * Representation value of disabled csvenvelopes
     *
     */
    const CACHE_TAG = 'csvenvelopes';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'kensium_csvenvelopes';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getCsvenvelopes() in this case
     *
     * @var string
     */
    protected $_eventObject = 'csvenvelopes';

    /**
     * Store csvenvelopes contents per store view
     *
     * @var array
     */
    protected $_contents = [];

    /**
     * Initialize csvenvelopes model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kensium\Csvenvelopes\Model\ResourceModel\Csvenvelopes');
    }

    /**
     * Get all existing csvenvelopes contents
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
     * Save csvenvelopes content, bind csvenvelopes to catalog and sales rules after csvenvelopes save
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
        if ($this->hasCsvenvelopesCatalogRules()) {
            $this->_getResource()->saveCatalogRules($this->getId(), $this->getCsvenvelopesCatalogRules());
        }
        if ($this->hasCsvenvelopesSalesRules()) {
            $this->_getResource()->saveSalesRules($this->getId(), $this->getCsvenvelopesSalesRules());
        }
        return parent::afterSave();
    }

    /**
     * Validate some data before saving
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */


    /**
     * Collect store ids in which current csvenvelopes has content
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
    public function getCsvenvelopesCollection()
    {
        return $this->_getResource()->getCsvenvelopesCollection();
    }
}
