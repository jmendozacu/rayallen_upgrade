<?php

/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\DataFeedManager\Model\ResourceModel;

/**
 * @copyright Wyomind 2016
 */
class Images extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected $_storeId = 0;

    public function _construct()
    {
        $this->_init('datafeedmanager_feeds', 'id');
    }

    
    public function getImages()
    {
        $galValueTable = \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Media::GALLERY_VALUE_TABLE;
        $galTable = \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Media::GALLERY_TABLE;

        $select = $this->getConnection()->select();
        $select->distinct("value")
                ->from(["main" => $this->getTable($galTable)])
                ->joinLeft(["cpemgv" => $this->getTable($galValueTable)], "cpemgv.value_id = main.value_id", ["cpemgv.position", "cpemgv.disabled","cpemgv.entity_id"])
                ->where("value<>TRIM('') AND(store_id=" . $this->_storeId . " OR  store_id=0)")
                ->order(["position", "value_id"])
                ->group(["value_id"]);

        $gallery = [];
        $mediaGallery = $this->getConnection()->fetchAll($select);
        foreach ($mediaGallery as $media) {
            if ($media["value"] != null && $media["value"] != "") {
                $gallery[$media["entity_id"]]["src"][] = $media["value"];
                $gallery[$media["entity_id"]]["disabled"][] = $media["disabled"];
            }
        }
        unset($mediaGallery);
        return $gallery;
    }

    
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }
}
