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

final class AlwaysValid implements TemplateInterface, RuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTemplates(): array
    {
        return [
            self::MODE_AFFIRMATIVE => [
                self::TEMPLATE_STANDARD => '{{name}} is always valid',
            ],
            self::MODE_NEGATIVE => [
                self::TEMPLATE_STANDARD => '{{name}} is never valid',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validate($input): ResultInterface
    {
        return new Result(true, $this, $input);
    }
}
