<?php

namespace Respect\Validation\Rules;

/**
 * @covers Respect\Validation\Rules\Key
 */
class KeyTest extends RuleTestCase
{
    public function testShouldAcceptArgumentsOnConstructor()
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

        $input = [];

        $result = $rule->validate($input);

        $this->assertFalse($result->isValid());
    }

    public function testShouldSetKeyTemplateWhenKeyDoesNotExist()
    {
        $rule = new Key('foo', $this->getRuleMock(), true);

        $input = [];

        $result = $rule->validate($input);

        $this->assertSame(Key::TEMPLATE_KEY, $result->getProperty('keyMode'));
    }

    public function testShouldApplyKeyRuleWhenKeyDoesExist()
    {
        $input = ['foo' => true];

        $ruleMock = $this->getRuleMock();
        $ruleMock
            ->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($this->getResultMock()));

        $rule = new Key('foo', $ruleMock, true);
        $rule->validate($input);
    }

    public function providerForValidInput()
    {
        $invalidRuleMock = $this->getInvalidRuleMock();
        $validRuleMock = $this->getValidRuleMock();

        return [
            'Mandatory and valid rule' => [new Key('foo', $validRuleMock, true), ['foo' => 'whatever']],
            'Not mandatory and missing key' => [new Key('foo', $invalidRuleMock, false), []],
        ];
    }

    public function providerForInvalidInput()
    {
        $invalidRuleMock = $this->getInvalidRuleMock();
        $validRuleMock = $this->getValidRuleMock();

        return [
            'Mandatory and invalid rule' => [new Key('foo', $invalidRuleMock, true), ['foo' => 'whatever']],
            'Mandatory and missing key' => [new Key('foo', $validRuleMock, true), []],
            'Not mandatory and invalid rule' => [new Key('foo', $invalidRuleMock, false), ['foo' => 'whatever']],
        ];
    }
}
