<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Respect\Validation\Rules;

use Respect\Validation\ResultInterface;

/**
 * Default interface for rules.
 */
interface RuleInterface
{
    /**
     * Validates the rule against the given input.
     *
     * @param mixed $input
     *
     * @return ResultInterface
     */
    public function validate($input): ResultInterface;
}
