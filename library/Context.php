<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation;

final class Context implements ContextInterface
{
    /**
     * @var mixed
     */
    private $input;

    /**
     * @var array
     */
    private $properties;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var callable
     */
    private $translator;

    /**
     * @param mixed    $input
     * @param array    $properties
     * @param Factory  $factory
     * @param callable $translator
     */
    public function __construct($input, array $properties, Factory $factory, callable $translator)
    {
        $this->input = $input;
        $this->properties = $properties;
        $this->factory = $factory;
        $this->translator = $translator;
    }

    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return callable
     */
    public function getTranslator()
    {
        return $this->translator;
    }
}
