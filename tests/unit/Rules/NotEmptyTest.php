<?php

namespace Respect\Validation\Rules;

/**
 * @covers Respect\Validation\Rules\NotEmpty
 */
class NotEmptyTest extends RuleTestCase
{
    public function providerForValidInput()
    {
        $rule = new NotEmpty();

        return [
            [$rule, ' '],
            [$rule, '0.0'],
            [$rule, ['']],
            [$rule, [' ']],
            [$rule, [0]],
            [$rule, ['0']],
            [$rule, [false]],
            [$rule, [[''], [0]]],
            [$rule, -1],
        ];
    }

    public function providerForInvalidInput()
    {
        $rule = new NotEmpty();

        return [
            [$rule, null],
            [$rule, ''],
            [$rule, []],
            [$rule, 0],
            [$rule, '0'],
            [$rule, 0],
            [$rule, false],
        ];
    }
}
