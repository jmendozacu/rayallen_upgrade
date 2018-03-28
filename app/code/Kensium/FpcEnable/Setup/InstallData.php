<?php
/**
 * @category   Amconnector
 * @package    Kensium_FpcEnable
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

$object_manager = \Magento\Framework\App\ObjectManager::getInstance();
$stateInterface = $object_manager->get('Magento\Framework\App\Cache\StateInterface');
$stateInterface->setEnabled('full_page',1);
$stateInterface->persist();
