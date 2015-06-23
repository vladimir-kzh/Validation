<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

/**
 * Validates if the given input is not empty.
 */
abstract class AbstractProxy implements Assertable
{
    /**
     * @var Assertable
     */
    private $rule;

    /**
     * @param Assertable $rule
     */
    public function __construct(Assertable $rule)
    {
        $this->rule = $rule;
    }

    /**
     * @return Assertable
     */
    public function getRule()
    {
        return $this->rule;
    }
}
