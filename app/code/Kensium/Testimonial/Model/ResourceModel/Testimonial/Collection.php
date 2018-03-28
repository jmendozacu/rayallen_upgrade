<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Testimonial Resource Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Kensium\Testimonial\Model\ResourceModel\Testimonial;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize testimonial resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kensium\Testimonial\Model\Testimonial', 'Kensium\Testimonial\Model\ResourceModel\Testimonial');
        $this->_map['fields']['testimonial_id'] = 'main_table.testimonial_id';
    }

    /**
     * Add stores column
     *
     * @return $this
     */
    protected function _afterLoad()
    {

        parent::_afterLoad();
        // fetch testimonial types from comma-separated
        return $this;
    }

    /**
     * Set add stores column flag
     *
     * @return $this
     */
    public function addStoresVisibility()
    {
        $this->setFlag('add_stores_column', true);
        return $this;
    }

    /**
     * Collect and set stores ids to each collection item
     * Used in testimonials grid as Visible in column info
     *
     * @return $this
     */
    protected function _addStoresVisibility()
    {
        $testimonialIds = $this->getColumnValues('testimonial_id');
        $testimonialsStores = [];
        if (sizeof($testimonialIds) > 0) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                $this->getTable('kensium_testimonial_content'),
                ['store_id', 'testimonial_id']
            )->where(
                'testimonial_id IN(?)',
                $testimonialIds
            );
            $testimonialsRaw = $connection->fetchAll($select);

            foreach ($testimonialsRaw as $testimonial) {
                if (!isset($testimonialsStores[$testimonial['testimonial_id']])) {
                    $testimonialsStores[$testimonial['testimonial_id']] = [];
                }
                $testimonialsStores[$testimonial['testimonial_id']][] = $testimonial['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($testimonialsStores[$item->getId()])) {
                $item->setStores($testimonialsStores[$item->getId()]);
            } else {
                $item->setStores([]);
            }
        }

        return $this;
    }

    /**
     * Add Filter by store
     *
     * @param int|array $storeIds
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($storeIds, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter')) {
            if ($withAdmin) {
                $storeIds = [0, $storeIds];
            }

            $this->getSelect()->join(
                ['store_table' => $this->getTable('kensium_testimonial_content')],
                'main_table.testimonial_id = store_table.testimonial_id',
                []
            )->where(
                'store_table.store_id IN (?)',
                $storeIds
            )->group(
                'main_table.testimonial_id'
            );

            $this->setFlag('store_filter', true);
        }
        return $this;
    }

    /**
     * Add filter by testimonials
     *
     * @param array $testimonialIds
     * @param bool $exclude
     * @return $this
     */
    public function addTestimonialIdsFilter($testimonialIds, $exclude = false)
    {
        $this->addFieldToFilter('main_table.testimonial_id', [$exclude ? 'nin' : 'in' => $testimonialIds]);
        return $this;
    }

}
