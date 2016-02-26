<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use Respect\Validation\ContextInterface;
use Respect\Validation\Helpers\OptionalHelper;
use Respect\Validation\Result;

/**
 * Validates if the given context is not optional.
 */
final class NotOptional implements RuleInterface
{
    use OptionalHelper;

    /**
     * {@inheritdoc}
     */
    public function getTemplates()
    {
        return [
            self::MODE_AFFIRMATIVE => [
                self::TEMPLATE_STANDARD => '{{placeholder}} is required',
            ],
            self::MODE_NEGATIVE => [
                self::TEMPLATE_STANDARD => '{{placeholder}} is not required',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ContextInterface $context)
    {
        return new Result(!$this->isOptional($context->getInput()), $this, $context);
    }
}
