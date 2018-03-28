<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Testimonial resource module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Kensium\Testimonial\Model\ResourceModel;

class Testimonial extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize testimonial resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('kensium_testimonial', 'testimonial_id');
    }

    /**
     * @return array
     *
     */
    public function getTestimonialCollection()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('kensium_testimonial'),
            ['*']
        )->where(
            'status =(?)',
            1
        );
        return $connection->fetchAll($select);
    }
}
