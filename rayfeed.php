<?php
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1');

error_reporting(E_ALL);
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/RayDataFeed.php';
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
$obj = $bootstrap->getObjectManager();
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('RayDataFeed');
$bootstrap->run($app);
