<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use Respect\Validation\Exceptions\NotBlankException;

/**
 * Validates if the given input is not blank.
 */
final class NotBlank implements Assertable
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isNotBlank($value)
    {
        if (is_numeric($value)) {
            return $value != 0;
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        if (is_array($value)) {
            $value = array_filter($value, __METHOD__);
        }

        return !empty($value);
    }

    /**
     * {@inheritDoc}
     */
    public function assert($input)
    {
        if ($this->isNotBlank($input)) {
            return;
        }

        throw new NotBlankException(['input' => $input]);
    }
}
