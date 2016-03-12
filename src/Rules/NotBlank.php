<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Respect\Validation\Rules;

use Respect\Validation\Result;
use Respect\Validation\ResultInterface;

/**
 * Validates if the given input is not blank.
 */
final class NotBlank implements TemplateInterface, RuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTemplates(): array
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
    public function validate($input): ResultInterface
    {
        return new Result($this->isNotBlank($input), $this, $input);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isNotBlank($value): bool
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
