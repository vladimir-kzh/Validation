<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

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
     * @var array
     */
    protected $label;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var FactoryInterface
     */
    protected static $defaultFactory;

    /**
     * Creates a new validator.
     *
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory = null)
    {
        $this->factory = $factory ?: static::getDefaultFactory();
    }

    /**
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Returns the default factory.
     *
     * @return FactoryInterface
     */
    public static function getDefaultFactory()
    {
        if (null === static::$defaultFactory) {
            static::$defaultFactory = new Factory();
        }

        return static::$defaultFactory;
    }

    /**
     * Defines the default factory.
     *
     * @param FactoryInterface $factory
     */
    public static function setDefaultFactory(FactoryInterface $factory)
    {
        static::$defaultFactory = $factory;
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
        $this->label = $label;

        return $this;
    }

    /**
     * Returns the label of the current validation chain.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
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
        $validator = self::create();
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
    public function isValid($input)
    {
        $result = $this->validate($input);

        return $result->isValid();
    }

    private function addProperties(ResultInterface $result, $properties)
    {
        $result->setProperty('label', $this->label);
        foreach ($properties as $key => $value) {
            $result->setProperty($key, $value);
        }
    }

    /**
     * @param mixed $input
     * @param array $properties
     *
     * @throws ValidationException
     */
    public function check($input, array $properties = [])
    {
        foreach ($this->getRules() as $childRule) {
            $result = $childRule->validate($input);

            if ($result->isValid()) {
                continue;
            }

            $this->addProperties($result, $properties);

            throw $this->factory->createFilteredException($result);
        }
    }

    /**
     * @param mixed $input
     * @param array $properties
     *
     * @throws ValidationException
     */
    public function assert($input, array $properties = [])
    {
        $result = $this->validate($input);

        if ($result->isValid()) {
            return;
        }

        $this->addProperties($result, $properties);

        throw $this->factory->createException($result);
    }

    /**
     * @param mixed $input
     *
     * @return bool
     */
    public function __invoke($input)
    {
        return $this->isValid($input);
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
