<?php

namespace Respect\Validation\Rules;

/**
 * @covers Respect\Validation\Rules\NotBlank
 */
class NotBlankTest extends RuleTestCase
{
    public function providerForValidInput()
    {
        $rule = new NotBlank();

        return [
            [$rule, -1],
            [$rule, 1],
            [$rule, true],
            [$rule, '.'],
            [$rule, '   .  '],
        ];
    }

    public function providerForInvalidInput()
    {
        $rule = new NotBlank();

        return [
            [$rule, null],
            [$rule, ''],
            [$rule, []],
            [$rule, ' '],
            [$rule, 0],
            [$rule, '0'],
            [$rule, 0],
            [$rule, '0.0'],
            [$rule, false],
            [$rule, ['']],
            [$rule, [' ']],
            [$rule, [0]],
            [$rule, ['0']],
            [$rule, [false]],
            [$rule, [[''], [0]]],
        ];
    }
}
