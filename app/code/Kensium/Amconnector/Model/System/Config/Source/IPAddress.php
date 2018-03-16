<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\System\Config\Source;

use Magento\Framework\HTTP\PhpEnvironment\ServerAddress;
class IPAddress
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\ServerAddress
     **/
    protected $_serverAddress;

    /**
     * @param ServerAddress $_serverAddress
     */
    public function __construct(
        ServerAddress $_serverAddress
    )
    {
        $this->_serverAddress = $_serverAddress;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $serverAddress = $this->_serverAddress->getServerAddress();
        $data[] = array('value' => $serverAddress, 'label' => $serverAddress);
        return $data;
    }
}
