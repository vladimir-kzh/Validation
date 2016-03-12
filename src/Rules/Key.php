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
 * Validates if the given input is not empty.
 */
final class Key implements TemplateInterface, RuleInterface
{
    const TEMPLATE_KEY = 1;

    /**
     * @var mixed
     */
    private $key;

    /**
     * @var RuleInterface
     */
    private $rule;

    /**
     * @var bool
     */
    private $mandatory;

    /**
     * @param mixed         $key
     * @param RuleInterface $rule
     * @param bool          $mandatory
     */
    public function __construct($key, RuleInterface $rule, bool $mandatory)
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->mandatory = $mandatory;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return RuleInterface
     */
    public function getRule(): RuleInterface
    {
        return $this->rule;
    }

    /**
     * @return bool
     */
    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplates(): array
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
    public function validate($input): ResultInterface
    {
        $keyExists = array_key_exists($this->key, $input);

        $result = new Result(
            ($keyExists || !$this->isMandatory()),
            $this,
            $input,
            ['key' => $this->key, 'keyTemplate' => self::TEMPLATE_KEY]
        );

        if ($result->isValid() && $keyExists) {
            $keyInput = $input[$this->key];

            $keyResult = $this->rule->validate($keyInput);
            $keyResult->appendTo($result);
            $keyResult->setProperty('label', $this->key);

            $result->appendChild($keyResult);
            $result->setProperty('keyTemplate', self::TEMPLATE_STANDARD);
            $result->setValid($keyResult->isValid());
        }

        return $result;
    }
}
