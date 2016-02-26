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
 * Validates if the given context is not blank.
 */
final class NotBlank implements RuleInterface
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
        return new Result($this->isNotBlank($context->getInput()), $this, $context);
    }

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
}
