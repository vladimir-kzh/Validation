<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Respect\Validation;

use Respect\Validation\Rules\RuleInterface;
use Respect\Validation\Rules\TemplateInterface;
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
     * @var mixed
     */
    private $input;

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
        'keyMode' => TemplateInterface::MODE_AFFIRMATIVE,
        'keyTemplate' => TemplateInterface::TEMPLATE_STANDARD,
    ];

    /**
     * @param bool          $isValid
     * @param RuleInterface $rule
     * @param mixed         $input
     * @param array         $properties
     */
    public function __construct(bool $isValid, RuleInterface $rule, $input, array $properties = [])
    {
        $this->isValid = $isValid;
        $this->rule = $rule;
        $this->input = $input;
        $this->properties = $properties;
        $this->children = new SplObjectStorage();
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function setValid(bool $isValid)
    {
        $this->isValid = $isValid;
    }

    /**
     * @return RuleInterface
     */
    public function getRule(): RuleInterface
    {
        return $this->rule;
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
    public function getProperties(): array
    {
        return $this->properties + $this->defaultProperties;
    }

    /**
     * @return SplObjectStorage
     */
    public function getChildren(): SplObjectStorage
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children->count() > 0;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getProperty(string $name)
    {
        $properties = $this->getProperties();
        if (!array_key_exists($name, $properties)) {
            throw new \OutOfBoundsException(sprintf('Property "%s" is missing', $name));
        }

        return $properties[$name];
    }

    public function hasProperty(string $name): bool
    {
        return array_key_exists($name, $this->getProperties());
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setProperty(string $name, $value)
    {
        $this->properties[$name] = $value;

        if ($name === 'message') {
            return;
        }

        foreach ($this->getChildren() as $childResult) {
            if ($childResult->hasProperty($name)) {
                continue;
            }

            $childResult->setProperty($name, $value);
        }
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
