<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reminder\Model\Rule\Condition;

class CartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reminder\Model\Rule\Condition\Cart
     */
    protected $_model;

    /**
     * @dataProvider daysDiffConditionDataProvider
     */
    public function testDaysDiffCondition($operator, $value, $expectedResult)
    {
        $dateModelMock = $this->getMock(
            'Magento\Framework\Stdlib\DateTime\DateTime',
            ['gmtDate'],
            [],
            '',
            false
        );
        $dateModelMock->expects($this->atLeastOnce())->method('gmtDate')->will($this->returnValue('2013-12-24'));

        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Reminder\Model\Rule\Condition\Cart',
            ['dateModel' => $dateModelMock]
        );
        $this->_model->setOperator($operator);
        $this->_model->setValue($value);

        $where = $this->_model->getConditionsSql(0, 0)->getPart('where');
        $this->assertContains($expectedResult, $where[2]);
    }

    /**
     * @return array
     */
    public function daysDiffConditionDataProvider()
    {
        return [
            ['>=', '1', 'AND ((TO_DAYS(\'2013-12-24 00:00:00\') - TO_DAYS(quote.updated_at)) >= 1)'],
            ['>', '1', 'AND ((TO_DAYS(\'2013-12-24 00:00:00\') - TO_DAYS(quote.updated_at)) > 1)'],
            ['>=', '0', 'AND ((TO_DAYS(\'2013-12-24 00:00:00\') - TO_DAYS(quote.updated_at)) >= 0)'],
            ['>', '0', 'AND ((TO_DAYS(\'2013-12-24 00:00:00\') - TO_DAYS(quote.updated_at)) > 0)']
        ];
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testDaysDiffConditionException()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Reminder\Model\Rule\Condition\Cart'
        );
        $this->_model->setOperator('');
        $this->_model->setValue(-1);
        $this->_model->getConditionsSql(0, 0);
    }
}
