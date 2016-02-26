<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use RecursiveIteratorIterator;
use Respect\Validation\ContextInterface;
use Respect\Validation\RecursiveResultIterator;
use Respect\Validation\Result;

/**
 * Negates any rule.
 */
final class Not implements RuleInterface
{
    /**
     * @var RuleInterface
     */
    private $rule;

    /**
     * @param RuleInterface $rule
     */
    public function __construct(RuleInterface $rule)
    {
        $this->rule = $rule;
    }

    /**
     * @return RuleInterface
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplates()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ContextInterface $context)
    {
        $result = $this->getRule()->apply($context);
        $resultIterator = new RecursiveResultIterator($result);
        $iteratorIterator = new RecursiveIteratorIterator($resultIterator, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iteratorIterator as $childResult) {
            $this->reverse($childResult);
        }
        $this->reverse($result);

        return $result;
    }

    /**
     * @param Result $result
     */
    public function reverse(Result $result)
    {
        $keyMode = self::MODE_NEGATIVE;
        if ($result->getProperty('keyMode') == self::MODE_NEGATIVE) {
            $keyMode = self::MODE_AFFIRMATIVE;
        }

        $result->setProperty('keyMode', $keyMode);
        $result->setValid(!$result->isValid());
    }
}
