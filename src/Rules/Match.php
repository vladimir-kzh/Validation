<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Respect\Validation\Rules;

use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Result;
use Respect\Validation\ResultInterface;

final class Match implements TemplateInterface, RuleInterface
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @param string $pattern
     */
    public function __construct(string $pattern)
    {
        if (!preg_match('/^(.).*\1[imsxeADSUXJu]*$/', $pattern)) {
            throw new ComponentException(sprintf('"%s" is not a valid regular expression pattern', $pattern));
        }

        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplates(): array
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
    public function validate($input): ResultInterface
    {
        $isValid = is_scalar($input) && preg_match($this->pattern, (string) $input);

        return new Result($isValid, $this, $input, ['pattern' => $this->getPattern()]);
    }
}
