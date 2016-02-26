<?php

namespace Respect\Validation\Rules;

use Respect\Validation\Context;
use Respect\Validation\Factory;
use Respect\Validation\Result;

/**
 * @covers Respect\Validation\Rules\Match
 */
class MatchTest extends RuleTestCase
{
    public function testShouldAcceptPatternOnConstructor()
    {
        $pattern = '/[a-z]/';

        $rule = new Match($pattern);

        $this->assertSame($pattern, $rule->getPattern());
    }

    public function providerForValidPattern()
    {
        return [
            ['/^[a-z]$/', 'a'],
            ['/[a-z]$/', '0980980 a'],
            ['/[2-9]{3}$/', '369'],
        ];
    }

    /**
     * @dataProvider providerForValidPattern
     */
    public function testShouldValidateWhenPatternMatches($pattern, $input)
    {
        $contextMock = $this->getContextMock();
        $contextMock
            ->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($input));

        $rule = new Match($pattern);

        $result = $rule->apply($contextMock);

        $this->assertTrue($result->isValid());
    }

    public function providerForInvalidPattern()
    {
        return [
            ['/^.$/', ''],
        ];
    }

    /**
     * @dataProvider providerForInvalidPattern
     */
    public function testShouldInvalidateWhenPatternMatches($pattern, $input)
    {
        $contextMock = $this->getContextMock();
        $contextMock
            ->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($input));

        $rule = new Match($pattern);

        $result = $rule->apply($contextMock);

        $this->assertFalse($result->isValid());
    }
}
