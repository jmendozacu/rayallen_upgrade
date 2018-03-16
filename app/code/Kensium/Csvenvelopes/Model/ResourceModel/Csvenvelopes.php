<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

/**
 * Csvenvelopes resource module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Kensium\Csvenvelopes\Model\ResourceModel;

class Csvenvelopes extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize csvenvelopes resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amconnector_csvenvelopes', 'csvenvelopes_id');
    }

    /**
     * @return array
     *
     */
    public function getCsvenvelopesCollection()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('amconnector_csvenvelopes'),
            ['*']
        )->where(
            'status =(?)',
            1
        );
        return $connection->fetchAll($select);
    }
}
