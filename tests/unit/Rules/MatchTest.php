<?php

namespace Respect\Validation\Rules;

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
            'No delimiters' => ['[a-z]'],
            'Wrong delimiters' => ['@./'],
            'Wrong modifiers' => ['/\w/k'],
        ];
    }

    /**
     * @dataProvider providerForValidPattern
     * @expectedException Respect\Validation\Exceptions\ComponentException
     * @expectedExceptionMessageRegExp /"[^"]+" is not a valid regular expression pattern/
     */
    public function testShouldThrowExceptionWhenPatternIsNotValid($pattern)
    {
        new Match($pattern);
    }

    public function providerForValidInput()
    {
        return [
            [new Match('#^[a-z]$#'), 'a'],
            [new Match('%[2-9]{3}$%'), '369'],
            [new Match(',[a-z]$,'), '0980980 a'],
            [new Match('//'), ''],
            [new Match('?^.$?'), 'a'],
            [new Match('@^\w+$@i'), 'HELLO'],
        ];
    }

    public function providerForInvalidInput()
    {
        return [
            [new Match('/^.$/'), ''],
        ];
    }
}
