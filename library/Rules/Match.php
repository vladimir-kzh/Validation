<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use Respect\Validation\Exceptions\MatchException;

final class Match implements RuleInterface
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritDoc}
     */
    public function assert($input)
    {
        if (is_scalar($input) && preg_match($this->pattern, $input)) {
            return;
        }

        throw new MatchException(['input' => $input, 'pattern' => $this->pattern]);
    }
}
