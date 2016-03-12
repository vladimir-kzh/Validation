<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Respect\Validation\Rules;

/**
 * Default interface for rules.
 */
interface TemplateInterface
{
    const MODE_AFFIRMATIVE = 1;
    const MODE_NEGATIVE = 0;

    const TEMPLATE_STANDARD = 0;

    /**
     * Returns the available template messages for the rule.
     *
     * @return array
     */
    public function getTemplates(): array;
}
