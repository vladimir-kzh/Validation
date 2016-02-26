<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Exceptions;

use InvalidArgumentException;
use IteratorAggregate;
use Respect\Validation\Helpers\NormalizerHelper;
use Respect\Validation\Result;
use SplObjectStorage;

class ValidationException extends InvalidArgumentException implements ExceptionInterface, IteratorAggregate
{
    const TEMPLATE_STANDARD = 0;

    const MODE_AFFIRMATIVE = 1;
    const MODE_NEGATIVE = 0;

    use NormalizerHelper {
        normalize as private;
    }

    /**
     * @var Result
     */
    private $result;

    /**
     * @var SplObjectStorage
     */
    private $children;

    /**
     * @param Result $result
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
        $this->message = $this->createMessage();
    }

    public function getResult()
    {
        return $this->result;
    }

    private function getValue(array $array, $key)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }

        return current($array);
    }

    /**
     * @return string
     */
    private function getMessageTemplate()
    {
        $result = $this->getResult();
        if ($result->hasProperty('message')) {
            return $result->getProperty('message');
        }

        $keyMode = $result->getProperty('keyMode');
        $keyTemplate = $result->getProperty('keyTemplate');
        $templates = $result->getRule()->getTemplates();
        $templatesMode = $this->getValue($templates, $keyMode);

        return $this->getValue($templatesMode, $keyTemplate);
    }

    /**
     * @return string
     */
    private function getPlaceholder()
    {
        if ($this->result->hasProperty('label')
            && ($label = $this->result->getProperty('label'))) {
            return $label;
        }

        if ($this->result->hasProperty('placeholder')) {
            return $this->result->getProperty('placeholder');
        }

        return $this->normalize($this->result->getContext()->getInput());
    }

    /**
     * @return string
     */
    private function createMessage()
    {
        $params = $this->result->getProperties();
        $params['placeholder'] = $this->getPlaceholder();

        return preg_replace_callback(
            '/{{(\w+)}}/',
            function ($match) use ($params) {
                $value = $match[0];
                if (array_key_exists($match[1], $params)) {
                    $value = $params[$match[1]];
                }

                if (!is_scalar($value)) {
                    return $this->normalize($value);
                }

                return $value;
            },
            call_user_func($this->result->getContext()->getTranslator(), $this->getMessageTemplate())
        );
    }

    /**
     * @return Factory
     */
    private function getFactory()
    {
        return $this->result->getContext()->getFactory();
    }

    /**
     * @return SplObjectStorage
     */
    public function getIterator()
    {
        if (!$this->children instanceof SplObjectStorage) {
            $this->children = $this->getFactory()->createExceptionTree($this->result);
        }

        return $this->children;
    }

    /**
     * @return string
     */
    public function getFullMessage()
    {
        $marker = '-';
        $exceptions = $this->getIterator();
        $messages = [sprintf('%s %s', $marker, $this->getMessage())];
        foreach ($exceptions as $exception) {
            $depth = $exceptions[$exception]['depth'];
            $prefix = str_repeat(' ', $depth * 2);
            $messages[] = sprintf('%s%s %s', $prefix, $marker, $exception->getMessage());
        }

        return implode(PHP_EOL, $messages);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        $messages = [$this->getMessage()];
        foreach ($this->getIterator() as $exception) {
            $messages[] = $exception->getMessage();
        }

        return $messages;
    }
}
