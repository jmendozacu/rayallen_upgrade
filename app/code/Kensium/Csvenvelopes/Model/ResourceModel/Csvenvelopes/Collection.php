<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

/**
 * Csvenvelopes Resource Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Kensium\Csvenvelopes\Model\ResourceModel\Csvenvelopes;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize csvenvelopes resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Kensium\Csvenvelopes\Model\Csvenvelopes', 'Kensium\Csvenvelopes\Model\ResourceModel\Csvenvelopes');
        $this->_map['fields']['csvenvelopes_id'] = 'main_table.csvenvelopes_id';
    }

    /**
     * Add stores column
     *
     * @return $this
     */
    protected function _afterLoad()
    {

        parent::_afterLoad();
        // fetch csvenvelopes types from comma-separated
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
     * Used in csvenvelopess grid as Visible in column info
     *
     * @return $this
     */
    protected function _addStoresVisibility()
    {
        $csvenvelopesIds = $this->getColumnValues('csvenvelopes_id');
        $csvenvelopessStores = [];
        if (sizeof($csvenvelopesIds) > 0) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                $this->getTable('amconnector_csvenvelopes_content'),
                ['store_id', 'csvenvelopes_id']
            )->where(
                'csvenvelopes_id IN(?)',
                $csvenvelopesIds
            );
            $csvenvelopessRaw = $connection->fetchAll($select);

            foreach ($csvenvelopessRaw as $csvenvelopes) {
                if (!isset($csvenvelopessStores[$csvenvelopes['csvenvelopes_id']])) {
                    $csvenvelopessStores[$csvenvelopes['csvenvelopes_id']] = [];
                }
                $csvenvelopessStores[$csvenvelopes['csvenvelopes_id']][] = $csvenvelopes['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($csvenvelopessStores[$item->getId()])) {
                $item->setStores($csvenvelopessStores[$item->getId()]);
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
                ['store_table' => $this->getTable('amconnector_csvenvelopes_content')],
                'main_table.csvenvelopes_id = store_table.csvenvelopes_id',
                []
            )->where(
                'store_table.store_id IN (?)',
                $storeIds
            )->group(
                'main_table.csvenvelopes_id'
            );

            $this->setFlag('store_filter', true);
        }
        return $this;
    }

    /**
     * Add filter by csvenvelopess
     *
     * @param array $csvenvelopesIds
     * @param bool $exclude
     * @return $this
     */
    public function addCsvenvelopesIdsFilter($csvenvelopesIds, $exclude = false)
    {
        $this->addFieldToFilter('main_table.csvenvelopes_id', [$exclude ? 'nin' : 'in' => $csvenvelopesIds]);
        return $this;
    }

}
