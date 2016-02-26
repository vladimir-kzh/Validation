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

/**
 * Validates if the given input is not empty.
 */
final class Key implements RuleInterface
{
    const TEMPLATE_KEY = 1;

    private $key;
    private $rule;
    private $mandatory;

    /**
     * @param mixed         $key
     * @param RuleInterface $rule
     * @param bool          $mandatory
     */
    public function __construct($key, RuleInterface $rule, $mandatory)
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->mandatory = (bool) $mandatory;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getRule()
    {
        return $this->rule;
    }

    public function isMandatory()
    {
        return $this->mandatory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplates()
    {
        return [
            self::MODE_AFFIRMATIVE => [
                self::TEMPLATE_STANDARD => '{{key}} key must be valid',
                self::TEMPLATE_KEY => '{{key}} key must be present',
            ],
            self::MODE_NEGATIVE => [
                self::TEMPLATE_STANDARD => '{{key}} key must not be valid',
                self::TEMPLATE_KEY => '{{key}} key must not be present',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ContextInterface $context)
    {
        $input = $context->getInput();

        $keyExists = array_key_exists($this->key, $input);

        $result = new Result(
            ($keyExists || !$this->isMandatory()),
            $this,
            $context,
            ['key' => $this->key, 'keyTemplate' => self::TEMPLATE_KEY]
        );

        if ($result->isValid() && $keyExists) {
            $keyContext = $context->getFactory()->createContext($input[$this->key], ['label' => $this->key]);

            $keyResult = $this->rule->apply($keyContext);
            $keyResult->appendTo($result);

            $result->appendChild($keyResult);
            $result->setProperty('keyTemplate', self::TEMPLATE_STANDARD);
            $result->setValid($keyResult->isValid());
        }

        return $result;
    }
}
