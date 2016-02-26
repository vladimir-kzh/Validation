<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation;

use Respect\Validation\Exceptions\ComponentException;

interface FactoryInterface
{
    /**
     * @return ContextInterface
     */
    public function createContext($input, array $properties);

    /**
     * @param ResultInterface $result
     *
     * @throws ComponentException
     *
     * @return ValidationException
     */
    public function createException(ResultInterface $result);

    /**
     * @param string $ruleName
     * @param array  $settings
     *
     * @throws ComponentException
     *
     * @return RuleInterface
     */
    public function createRule($ruleName, array $settings = []);
}
