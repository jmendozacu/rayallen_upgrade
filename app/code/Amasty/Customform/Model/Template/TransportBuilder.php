<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


namespace Amasty\Customform\Model\Template;

use Zend_Mime;
use \Magento\Framework\Mail\Template\TransportBuilder as Transport;

class TransportBuilder extends Transport
{
    /**
     * @param $body
     * @param null $filename
     * @param string $mimeType
     * @param string $disposition
     * @param string $encoding
     * @return $this
     */
    public function addAttachment(
        $body,
        $filename    = null,
        $mimeType    = Zend_Mime::TYPE_OCTETSTREAM,
        $disposition = Zend_Mime::DISPOSITION_ATTACHMENT,
        $encoding    = Zend_Mime::ENCODING_BASE64
    ) {
        $this->message->createAttachment(
            $body,
            $mimeType,
            $disposition,
            $encoding,
            $filename
        );

        return $this;
    }
}
