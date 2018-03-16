<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Orderstatus\Block;

class Orderstatus extends \Magento\Framework\View\Element\Template
{
 public function getAction()
 {
    return $this->getUrl('*/Save/Save');
 }
}
?>
