<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use Respect\Validation\ContextInterface;
use Respect\Validation\ResultInterface;

/**
 * Default interface for rules.
 */
interface RuleInterface
{
    const MODE_AFFIRMATIVE = 1;
    const MODE_NEGATIVE = 0;

    const TEMPLATE_STANDARD = 0;

    /**
     * Apply rule on the given context.
     *
     * @param ContextInterface $context
     *
     * @return ResultInterface
     */
    public function apply(ContextInterface $context);

    /**
     * Returns all available templates.
     *
     * @return array
     */
    public function getTemplates();
}
