<?php

namespace Respect\Validation\Rules;

use Respect\Validation\Context;
use Respect\Validation\Factory;
use Respect\Validation\Result;

/**
 * @covers Respect\Validation\Rules\Key
 */
class KeyTest extends RuleTestCase
{
    public function testShouldAcceptKeyRuleAndMandatoryOnConstructor()
    {
        $key = 'foo';
        $ruleMock = $this->getRuleMock();
        $mandatory = true;

        $rule = new Key($key, $ruleMock, $mandatory);

        $this->assertSame($key, $rule->getKey());
        $this->assertSame($ruleMock, $rule->getRule());
        $this->assertSame($mandatory, $rule->isMandatory());
    }

    public function testShouldResultInvalidWhenKeyDoesNotExist()
    {
        $rule = new Key('foo', $this->getRuleMock(), true);

        $contextMock = $this->getContextMock();
        $contextMock
            ->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue([]));

        $result = $rule->apply($contextMock);

        $this->assertFalse($result->isValid());
    }

    public function testShouldSetKeyTemplateWhenKeyDoesNotExist()
    {
        $rule = new Key('foo', $this->getRuleMock(), true);

        $contextMock = $this->getContextMock();
        $contextMock
            ->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue([]));
        $contextMock
            ->expects($this->once())
            ->method('getProperties')
            ->will($this->returnValue([]));

        $result = $rule->apply($contextMock);

        $this->assertSame(Key::TEMPLATE_KEY, $result->getProperty('keyMode'));
    }

    public function testShouldApplyKeyRuleWhenKeyDoesExist()
    {
        $factoryMock = $this->getFactoryMock();
        $factoryMock
            ->expects($this->once())
            ->method('createContext')
            ->will($this->returnValue($this->getContextMock()));

        $contextMock = $this->getContextMock();
        $contextMock
            ->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue(['foo' => true]));
        $contextMock
            ->expects($this->once())
            ->method('getFactory')
            ->will($this->returnValue($factoryMock));

        $ruleMock = $this->getRuleMock();
        $ruleMock
            ->expects($this->once())
            ->method('apply')
            ->will($this->returnValue($this->getResultMock()));

        $rule = new Key('foo', $ruleMock, true);
        $rule->apply($contextMock);
    }

    public function testShouldCreateAValidResultWhenRuleIsValid()
    {
        $factoryMock = $this->getFactoryMock();
        $factoryMock
            ->expects($this->once())
            ->method('createContext')
            ->will($this->returnValue($this->getContextMock()));

        $contextMock = $this->getContextMock();
        $contextMock
            ->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue(['foo' => true]));
        $contextMock
            ->expects($this->once())
            ->method('getFactory')
            ->will($this->returnValue($factoryMock));

        $ruleMock = $this->getRuleMock();
        $ruleMock
            ->expects($this->once())
            ->method('apply')
            ->will($this->returnValue($this->getResultMock()));

        $rule = new Key('foo', $ruleMock, true);
        $rule->apply($contextMock);
    }
}
