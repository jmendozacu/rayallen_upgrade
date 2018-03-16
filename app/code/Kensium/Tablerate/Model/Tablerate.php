<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Shipping table rates
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Kensium\Tablerate\Model;

use Magento\Framework\App\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Tablerate extends \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate
{

    /**
     * Return table rate array or false by rate request
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return array|bool
     */
    public function getRate(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $overPrice = 0;

        foreach ($request->getAllItems() as $item) {
            /**
             * Skip if this item is virtual
             */
            if ($item->getProduct()->isVirtual()) {
                continue;
            }

            /**
             * Children weight we calculate for parent
             */
            if ($item->getParentItem()) {
                continue;
            }

            $oversizeCollection = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Kensium\OverSize\Model\Oversizeship')->getCollection()->addFieldToFilter('sku', $item->getSku());
            if (count($oversizeCollection)) {
                foreach ($oversizeCollection as $rec) {
                    $overPrice = $overPrice + $rec->getData('price') * $item->getQty();
                    break;
                }
            }

        }

        $connection = $this->getConnection();
        $bind = [
            ':website_id' => (int)$request->getWebsiteId(),
            ':country_id' => $request->getDestCountryId(),
            ':region_id' => (int)$request->getDestRegionId(),
            ':postcode' => $request->getDestPostcode(),
        ];
        $select = $connection->select()->from(
            $this->getMainTable()
        )->where(
            'website_id = :website_id'
        )->order(
            ['dest_country_id DESC', 'dest_region_id DESC', 'dest_zip DESC']
        )->limit(
            1
        );

        // Render destination condition
        $orWhere = '(' . implode(
                ') OR (',
                [
                    "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = :postcode",
                    "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = ''",

                    // Handle asterix in dest_zip field
                    "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = '*'",
                    "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'",
                    "dest_country_id = '0' AND dest_region_id = :region_id AND dest_zip = '*'",
                    "dest_country_id = '0' AND dest_region_id = 0 AND dest_zip = '*'",
                    "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = ''",
                    "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = :postcode",
                    "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'"
                ]
            ) . ')';
        $select->where($orWhere);

        // Render condition by condition name
        if (is_array($request->getConditionName())) {
            $orWhere = [];
            $i = 0;
            foreach ($request->getConditionName() as $conditionName) {
                $bindNameKey = sprintf(':condition_name_%d', $i);
                $bindValueKey = sprintf(':condition_value_%d', $i);
                $orWhere[] = "(condition_name = {$bindNameKey} AND condition_value <= {$bindValueKey})";
                $bind[$bindNameKey] = $conditionName;
                $bind[$bindValueKey] = $request->getData($conditionName);
                $i++;
            }

            if ($orWhere) {
                $select->where(implode(' OR ', $orWhere));
            }
        } else {
            $bind[':condition_name'] = $request->getConditionName();
            $bind[':condition_value'] = $request->getData($request->getConditionName());

            $select->where('condition_name = :condition_name');
            $select->where('condition_value <= :condition_value');
        }

        $result = $connection->fetchRow($select, $bind);
        // Normalize destination zip code
        if ($result && $result['dest_zip'] == '*') {
            $result['dest_zip'] = '';
        }

        if ($result['website_id'] != '2' && $result['website_id'] != '4' && $request->getData($request->getConditionName()) >= 500) {
            $result['price'] = (5 / 100) * ($request->getData($request->getConditionName()));
        }
        if ($overPrice) {
            $result['price'] = $overPrice + $result['price'];
        }
        
        return $result;
    }


}
