<?php
/**
 * @category   Amconnector
 * @package    Kensium_Amconnector
 * @copyright  Copyright (c) 2016 Kensium Solution Pvt.Ltd. (http://www.kensiumsolutions.com/)
 */
namespace Kensium\Amconnector\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Date extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
protected $date;
public function __construct(
\Magento\Framework\Data\Form\Element\Date $date
)
{
$this->date=$date;
}
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
	$format = 'M/d/yyyy';
	$data = array(
            'name'      => $element->getName(),
            'html_id'   => $element->getId(),
	    'class'     => 'input-text admin__control-select',
	    'style'     => 'width: 300px;',
        );

	$this->date->setData($data);
        $this->date->setValue($element->getValue(),$format);
        $this->date->setFormat('M/d/yyyy');
        $this->date->setForm($element->getForm());

        return $this->date->getElementHtml();
    }
}
