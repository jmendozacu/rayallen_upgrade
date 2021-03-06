<?php
/**
 * Copyright © 2015 Kensium. All rights reserved.
 */

namespace Kensium\Synclog\Model\Resource\Productimagesynclog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kensium\Synclog\Model\Productimagesynclog', 'Kensium\Synclog\Model\Resource\Productimagesynclog');
    }
}

