<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation;

use Respect\Validation\Rules\RuleInterface;
use SplObjectStorage;

class Result implements ResultInterface
{
    /**
     * @bool
     */
    private $isValid;

    /**
     * @var RuleInterface
     */
    private $rule;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var array
     */
    private $properties = [];

    /**
     * @var SplObjectStorage
     */
    private $children;

    /**
     * @var array
     */
    private $defaultProperties = [
        'keyMode' => RuleInterface::MODE_AFFIRMATIVE,
        'keyTemplate' => RuleInterface::TEMPLATE_STANDARD,
    ];

    /**
     * @param bool          $isValid
     * @param RuleInterface $rule
     * @param ContextInterface       $context
     * @param array         $properties
     */
    public function __construct($isValid, RuleInterface $rule, ContextInterface $context, array $properties = [])
    {
        $this->isValid = $isValid;
        $this->rule = $rule;
        $this->context = $context;
        $this->properties = $properties;
        $this->children = new SplObjectStorage();
    }

    public function isValid()
    {
        return $this->isValid;
    }

    public function setValid($isValid)
    {
        $this->isValid = $isValid;
    }

    /**
     * @return RuleInterface
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @return ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties + $this->defaultProperties + $this->context->getProperties();
    }

    /**
     * @return SplObjectStorage
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return $this->children->count() > 0;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getProperty($name)
    {
        $properties = $this->getProperties();
        if (!array_key_exists($name, $properties)) {
            throw new \OutOfBoundsException(sprintf('Property "%s" is missing', $name));
        }

        return $properties[$name];
    }

    public function hasProperty($name)
    {
        return array_key_exists($name, $this->getProperties());
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * @param ResultInterface $result
     */
    public function appendTo(ResultInterface $result)
    {
        $result->appendChild($this);
    }

    /**
     * @param ResultInterface $childResult
     */
    public function appendChild(ResultInterface $childResult)
    {
        $this->children->attach($childResult);
    }
}
