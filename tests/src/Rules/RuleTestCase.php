<?php

namespace Respect\Validation\Rules;

use PHPUnit_Framework_TestCase;
use Respect\Validation\FactoryInterface;
use Respect\Validation\ResultInterface;

abstract class RuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * It is to provide constructor arguments and.
     *
     * @return array
     */
    abstract public function providerForValidInput();

    /**
     * @return array
     */
    abstract public function providerForInvalidInput();

    /**
     * @dataProvider providerForValidInput
     *
     * @param RuleInterface $rule
     * @param mixed         $input
     */
    public function testShouldValidateValidInput(RuleInterface $rule, $input)
    {
        $result = $rule->validate($input);

        $this->assertTrue($result->isValid());
    }

    /**
     * @dataProvider providerForInvalidInput
     *
     * @param RuleInterface $rule
     * @param mixed         $input
     */
    public function testShouldValidateInvalidInput(RuleInterface $rule, $input)
    {
        $result = $rule->validate($input);

        $this->assertFalse($result->isValid());
    }

    /**
     * @return RuleInterface
     */
    protected function getValidRuleMock()
    {
        $resultMock = $this->getResultMock();
        $resultMock
            ->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));

        $ruleMock = $this->getRuleMock();
        $ruleMock
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue($resultMock));

        return $ruleMock;
    }

    /**
     * @return RuleInterface
     */
    protected function getInvalidRuleMock()
    {
        $resultMock = $this->getResultMock();
        $resultMock
            ->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(false));

        $ruleMock = $this->getRuleMock();
        $ruleMock
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue($resultMock));

        return $ruleMock;
    }

    /**
     * @return RuleInterface
     */
    protected function getRuleMock()
    {
        return $this->getMock('Respect\\Validation\\Rules\\RuleInterface');
    }

    /**
     * @return FactoryInterface
     */
    protected function getFactoryMock()
    {
        return $this->getMock('Respect\\Validation\\FactoryInterface');
    }

    /**
     * @return ResultInterface
     */
    protected function getResultMock()
    {
        return $this->getMock('Respect\\Validation\\ResultInterface');
    }
}
