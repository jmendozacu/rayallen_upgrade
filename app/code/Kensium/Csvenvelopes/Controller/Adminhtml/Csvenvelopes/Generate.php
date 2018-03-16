<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */

namespace Kensium\Csvenvelopes\Controller\Adminhtml\Csvenvelopes;

use Magento\Framework\Filesystem\DriverInterface;
class Generate extends \Kensium\Csvenvelopes\Controller\Adminhtml\Csvenvelopes
{
    public function execute()
    {
        $directoryName = BP .'/apiclient/';
        if(!$directoryName){
            mkdir($directoryName,0777);
        }
        //@chmod($directoryName,DriverInterface::WRITEABLE_FILE_MODE);


        $csvFilePath = fopen($directoryName.'envelopes.csv', "w") or die("Unable to open file!");

        $model = $this->_objectManager->create('Kensium\Csvenvelopes\Model\Csvenvelopes')->getCollection();

        $envelopeData = '';
        $envelopeData .= 'SNO|';
        $envelopeData .= 'ENVCODE|';
        $envelopeData .= 'ENV ENTITY|';
        $envelopeData .= 'ENV TYPE|';
        $envelopeData .= 'ENV VERSION|';
        $envelopeData .= 'ENV NAME|';
        $envelopeData .= 'METHOD NAME|';
        $envelopeData .= 'ENVELOPE';
        $envelopeData .= "\n";

        $i = 0;
        foreach($model as $csvdata){

            $envelopeData .= $i;
            $envelopeData .= "|";
            $envelopeData .= $csvdata['envcode'];
            $envelopeData .= "|";
            $envelopeData .= $csvdata['enventity'];
            $envelopeData .= "|";
            $envelopeData .= $csvdata['envtype'];
            $envelopeData .= "|";
            $envelopeData .= $csvdata['envversion'];
            $envelopeData .= "|";
            $envelopeData .= $csvdata['envname'];
            $envelopeData .= "|";
            $envelopeData .= $csvdata['methodname'];
            $envelopeData .= "|";
            $envelopeData .= '"'.str_replace('"','""',$csvdata['envelope']).'"';
            $envelopeData .= "\n";
            $i++;

        }
        if(fwrite($csvFilePath, $envelopeData)){
            $this->messageManager->addSuccess(__('Envelopes CSV generated successfully.'));
        }
        $this->_redirect('kensium_csvenvelopes/*/');
    }




}
