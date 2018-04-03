<?php
/**
 * Copyright © 2015 Kensium. All rights reserved.
 */

namespace Kensium\Synclog\Model\Resource\Productimagesynclogdetails;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kensium\Synclog\Model\Productimagesynclogdetails', 'Kensium\Synclog\Model\Resource\Productimagesynclogdetails');
    }
}

