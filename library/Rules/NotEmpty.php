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

/**
 * Validates if the given context is not empty.
 */
final class NotEmpty implements RuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTemplates()
    {
        return [
            self::MODE_AFFIRMATIVE => [
                self::TEMPLATE_STANDARD => '{{placeholder}} must not be empty',
            ],
            self::MODE_NEGATIVE => [
                self::TEMPLATE_STANDARD => '{{placeholder}} must be empty',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ContextInterface $context)
    {
        $input = $context->getInput();

        return new Result(!empty($input), $this, $context);
    }
}
