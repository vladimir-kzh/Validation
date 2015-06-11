<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use Respect\Validation\Exceptions\NotEmptyException;

/**
 * Validates if the given input is not empty.
 */
final class NotEmpty implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function assert($input)
    {
        if (!empty($input)) {
            return;
        }

        throw new NotEmptyException(['input' => $input]);
    }
}
