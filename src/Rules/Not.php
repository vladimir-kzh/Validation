<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Respect\Validation\Rules;

use RecursiveIteratorIterator;
use Respect\Validation\RecursiveResultIterator;
use Respect\Validation\Result;
use Respect\Validation\ResultInterface;

/**
 * Negates any rule rule.
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
    public function getRule(): RuleInterface
    {
        return $this->rule;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($input): ResultInterface
    {
        $result = $this->getRule()->validate($input);
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
        $keyMode = TemplateInterface::MODE_NEGATIVE;
        if ($result->getProperty('keyMode') == TemplateInterface::MODE_NEGATIVE) {
            $keyMode = TemplateInterface::MODE_AFFIRMATIVE;
        }

        $result->setProperty('keyMode', $keyMode);
        $result->setValid(!$result->isValid());
    }
}
