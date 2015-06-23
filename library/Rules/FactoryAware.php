<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Factory;

interface FactoryAware
{
    /**
     * @param Factory $factory
     */
    public function setFactory(Factory $factory);

    /**
     * @throws ComponentException
     *
     * @return Factory
     */
    public function getFactory();
}
