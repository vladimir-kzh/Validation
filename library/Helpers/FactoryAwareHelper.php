<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Helpers;

use Respect\Validation\Factory;
use Respect\Validation\Exceptions\ComponentException;

trait FactoryAwareHelper
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @param Factory $factory
     */
    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return Factory
     */
    public function getFactory()
    {
        if (!$this->factory instanceof Factory) {
            throw new ComponentException('There is no defined factory.');
        }

        return $this->factory;
    }
}
