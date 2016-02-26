<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use Respect\Validation\ContextInterface;
use Respect\Validation\Result;
use SplObjectStorage;

/**
 * Will validate if all inner validators validates.
 */
class AllOf implements RuleInterface
{
    /**
     * @var SplObjectStorage
     */
    private $rules;

    public function __construct() // variadic params with type constraints are not supported in HHVM
    {
        $this->addRules(func_get_args());
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->getRules()->count();
    }

    /**
     * @param RuleInterface $rule
     *
     * @return self
     */
    public function addRule(RuleInterface $rule)
    {
        $this->getRules()->attach($rule);

        return $this;
    }

    public function addRules(array $rules)
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    /**
     * @return SplObjectStorage
     */
    public function getRules()
    {
        if (!$this->rules instanceof SplObjectStorage) {
            $this->rules = new SplObjectStorage();
        }

        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplates()
    {
        return [
            self::MODE_AFFIRMATIVE => [
                self::TEMPLATE_STANDARD => 'All rules must pass for {{placeholder}}',
            ],
            self::MODE_NEGATIVE => [
                self::TEMPLATE_STANDARD => 'All rules must not pass for {{placeholder}}',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ContextInterface $context)
    {
        $result = new Result(true, $this, $context);
        foreach ($this->getRules() as $childRule) {
            $childResult = $childRule->apply($context);
            $childResult->appendTo($result);

            $result->setValid($result->isValid() && $childResult->isValid());
        }

        return $result;
    }
}
