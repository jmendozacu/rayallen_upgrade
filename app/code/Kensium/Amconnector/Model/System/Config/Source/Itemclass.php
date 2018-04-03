<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\System\Config\Source;
class Itemclass
{
    /**
     * @param \Kensium\Amconnector\Model\ResourceModel\Product $resourceModel
     */
    public function __construct(
        \Kensium\Amconnector\Model\ResourceModel\Product $resourceModel
    )
    {
        $this->resourceModel = $resourceModel;
    }
    /**
     * @return array
     */
    public function toOptionArray()
    {
       $attributeSets = $this->resourceModel->getAllAttributeSets();
        $resluts = array();
        if(count($attributeSets) > 0) {
            foreach ($attributeSets as $attributeset) {
                $resluts[] = array('value' => $attributeset['attribute_set_id'], 'label' => $attributeset['attribute_set_name']);

            }
        }
        return $resluts;
    }
}
