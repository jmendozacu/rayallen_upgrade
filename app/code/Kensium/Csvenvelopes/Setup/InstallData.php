<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

$object_manager = \Magento\Framework\App\ObjectManager::getInstance();
$csvEnvelopeFile = BP.'/apiclient/acumatica_envelops.csv';
$handle = fopen($csvEnvelopeFile, "r");
$i = 0;
$envelopeData = array();
$data = array();
$rowCount = count(file($csvEnvelopeFile));

while (($data[] = fgetcsv($handle, 10000, "|")) !== FALSE) {
    if ($i == 0 || $data['0'] == '') {
    } else {
        $envelopeData[] = $data[$i];
    }

    $i++;
}
foreach($envelopeData as $envData){
    if(!empty($envData[1])) {
        $data = array(
            'envcode' => $envData[1],
            'enventity' => $envData[2],
            'envtype' => $envData[3],
            'envversion' => $envData[4],
            'envname' => $envData[5],
            'methodname' => $envData[6],
            'envelope' => $envData[8],
        );
        $model = $object_manager->get('Kensium\Csvenvelopes\Model\Csvenvelopes')->setData($data)->save();
    }
}
