<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use Respect\Validation\Helpers\OptionalHelper;

/**
 * Validates if the given input is not optional.
 */
final class Optional extends AbstractProxy
{
    use OptionalHelper;

    /**
     * {@inheritDoc}
     */
    public function assert($input)
    {
        if ($this->isOptional($input)) {
            return;
        }

        $this->getRule()->assert($input);
    }
}
