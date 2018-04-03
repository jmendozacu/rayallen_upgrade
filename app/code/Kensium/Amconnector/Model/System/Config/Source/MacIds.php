<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Model\System\Config\Source;

class MacIds
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $macIds = $this->getMacLinux();
        $macData = array();
        foreach ($macIds as $key => $value) {
            $macData[] = array('value' => $value, 'label' => $value);
        }
        return $macData;
    }

    /**
     * @return mac addresses
     */
    public function getMacLinux()
    {
        $iface = array();
        if (strtoupper(php_uname('s')) === 'LINUX') {
            exec('netstat -ie', $result);
            if (is_array($result)) {

                foreach ($result as $key => $line) {
                    if ($key > 0) {
                        $tmp = str_replace(" ", "", substr($line, 0, 10));
                        if ($tmp <> "") {
                            $macpos = strpos($line, "HWaddr");
                            if ($macpos !== false) {
                                $iface[] = strtolower(substr($line, $macpos + 7, 17));
                            }
                        }
                    }
                }
                return $iface;
            } else {
                return $iface;
            }
        } elseif (strtoupper(php_uname('s')) === 'DARWIN') {
            exec('ifconfig', $result);
            $ehters = array();
            if (is_array($result)) {
                foreach ($result as $key => $line) {
                    if ($key > 0) {
                        $tmp = str_replace(" ", "", substr($line, 0, 6));
                        if ($tmp <> "") {
                            $macpos = strpos($line, "ether");
                            if ($macpos !== false) {
                                $iface[] = strtolower(substr($line, $macpos + 6, 17));
                            }
                        }
                    }
                }
                return $iface;
            } else {
                return $iface;
            }
        }
    }
}
