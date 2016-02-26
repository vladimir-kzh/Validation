<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use Respect\Validation\ContextInterface;
use Respect\Validation\Result;

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
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplates()
    {
        return [
            self::MODE_AFFIRMATIVE => [
                self::TEMPLATE_STANDARD => '{{placeholder}} must match `{{pattern}}`',
            ],
            self::MODE_NEGATIVE => [
                self::TEMPLATE_STANDARD => '{{placeholder}} must not match `{{pattern}}`',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ContextInterface $context)
    {
        $input = $context->getInput();
        $isValid = is_scalar($input) && preg_match($this->pattern, $input);

        return new Result($isValid, $this, $context, ['pattern' => $this->getPattern()]);
    }
}
