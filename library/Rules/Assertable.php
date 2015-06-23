<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use Respect\Validation\Exceptions\ValidationException;

/**
 * Default interface for rules.
 */
interface Assertable
{
    /**
     * @param mixed $input
     *
     * @throws ValidationException
     */
    public function assert($input);
}
