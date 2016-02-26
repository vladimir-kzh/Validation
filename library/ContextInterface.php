<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation;

/**
 * Interface for contexts.
 *
 * This interface representes the context of the validation. It containts the
 * input, a Factory and some properties that may be used to improve of set the
 * behaviour of error messages.
 */
interface ContextInterface
{
    /**
     * Returns the factory of the context.
     *
     * @return Factory
     */
    public function getFactory();

    /**
     * Returns the defined input.
     *
     * @return mixed
     */
    public function getInput();

    /**
     * Returns some user-defined properties.
     *
     * @return array
     */
    public function getProperties();

    /**
     * Returns the a callback to be used as translator.
     *
     * @return callable
     */
    public function getTranslator();
}
