<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Rules\AllOf;
use Respect\Validation\Rules\RuleInterface;

/**
 * @method static Validator allOf(RuleInterface ...$rule)
 * @method static Validator key()
 * @method static Validator match()
 * @method static Validator notEmpty()
 * @method static Validator notBlank()
 * @method static Validator not(RuleInterface $rule)
 * @method static Validator notOptional()
 */
class Validator extends AllOf
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var Factory
     */
    protected static $defaultFactory;

    /**
     * Creates a new validator.
     *
     * @param Factory $factory
     */
    public function __construct(Factory $factory = null)
    {
        $this->factory = $factory ?: static::getDefaultFactory();
    }

    /**
     * Returns the default factory.
     *
     * @return Factory
     */
    public static function getDefaultFactory()
    {
        if (null === static::$defaultFactory) {
            static::$defaultFactory = new Factory();
        }

        return static::$defaultFactory;
    }

    /**
     * Defines the label of the current validation chain.
     *
     * @param string $label
     *
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = (string) $label;

        return $this;
    }

    /**
     * Returns the label of the current validation chain.
     *
     * @return string
     */
    public function getLabel()
    {
        return (string) $this->label;
    }

    /**
     * Creates a new validator chain with the called validation rule.
     *
     * @param string $ruleName
     * @param array  $arguments
     *
     * @return Validator
     */
    public static function __callStatic($ruleName, array $arguments)
    {
        $validator = new static();
        $validator->__call($ruleName, $arguments);

        return $validator;
    }

    /**
     * Creates and append a new validation rule to the chain using its name.
     *
     * @param string $ruleName
     * @param array  $arguments
     *
     * @return self
     */
    public function __call($ruleName, array $arguments)
    {
        $rule = $this->factory->createRule($ruleName, $arguments);

        $this->addRule($rule);

        return $this;
    }

    /**
     * @param mixed $input
     *
     * @return bool
     */
    public function validate($input)
    {
        try {
            $this->assert($input);
        } catch (ValidationException $exception) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $input
     *
     * @throws ValidationException
     */
    public function check($input)
    {
        try {
            $this->assert($input);
        } catch (ValidationException $exception) {
            $exception = $this->factory->createFilteredException($exception);
            $exception->label = $this->label;

            throw $exception;
        }
    }

    public function assert($input)
    {
        try {
            parent::assert($input);
        } catch (ValidationException $exception) {
            $exception->label = $this->label;
            $exception->children = $this->factory->createFilteredChildrenException($exception);

            throw $exception;
        }
    }

    /**
     * @param mixed $input
     *
     * @return bool
     */
    public function __invoke($input)
    {
        return $this->validate($input);
    }

    /**
     * Creates a new validator.
     *
     * @return Validator
     */
    public static function create()
    {
        return new static();
    }
}
