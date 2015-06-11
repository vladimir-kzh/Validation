<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use Respect\Validation\Exceptions\KeyException;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Validates if the given input is not empty.
 */
final class Key implements RuleInterface
{
    private $key;
    private $rule;
    private $mandatory;

    /**
     * @param mixed         $key
     * @param RuleInterface $rule
     * @param bool          $mandatory
     */
    public function __construct($key, RuleInterface $rule, $mandatory)
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->mandatory = (bool) $mandatory;
    }

    /**
     * {@inheritDoc}
     */
    public function assert($input)
    {
        if (!isset($input[$this->key])) {
            throw new KeyException(['input' => $input, 'key' => $this->key]);
        }

        try {
            $this->rule->assert($input);
        } catch (ValidationException $exception) {
            $exception->label = $this->key;
            $exception->input = $input[$this->key];

            throw $exception;
        }
    }
}
