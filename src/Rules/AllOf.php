<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Respect\Validation\Rules;

use Respect\Validation\Result;
use Respect\Validation\ResultInterface;

/**
 * Will validate if all inner validators validates.
 */
class AllOf implements TemplateInterface, RuleInterface
{
    /**
     * @var array
     */
    private $rules = [];

    public function __construct(RuleInterface ...$rules)
    {
        $this->addRules($rules);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->rules);
    }

    /**
     * @param RuleInterface $rule
     */
    public function addRule(RuleInterface $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * @param RuleInterface[] $rules
     */
    public function addRules(array $rules)
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplates(): array
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
    public function validate($input): ResultInterface
    {
        $result = new Result(true, $this, $input);
        foreach ($this->getRules() as $childRule) {
            $childResult = $childRule->validate($input);
            $childResult->appendTo($result);

            $result->setValid($result->isValid() && $childResult->isValid());
        }

        return $result;
    }
}
