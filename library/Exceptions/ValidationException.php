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
use SplObjectStorage;
use RecursiveIteratorIterator;

class ValidationException extends InvalidArgumentException implements Throwable, IteratorAggregate
{
    const MESSAGE_STANDARD = 0;

    const MODE_AFFIRMATIVE = 1;
    const MODE_NEGATIVE = 0;

    /**
     * @var array
     */
    private $context;

    /**
     * @var SplObjectStorage
     */
    private $children;

    /**
     * @param array $context
     */
    public function __construct(array $context)
    {
        if (!array_key_exists('input', $context)) {
            throw new ComponentException('Key "input" is required to create an exception');
        }

        $this->context = $context;
        $this->message = $this->createMessage();
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    // I guess it will need some recursion, not sure
    public function __set($name, $value)
    {
        $this->context[$name] = $value;

        $this->message = $this->createMessage();

        foreach ($this->getChildren() as $exception) {
            if ($exception->label) {
                continue;
            }
            $exception->label = $this->label;
        }
    }

    public function __get($name)
    {
        $value = null;
        if (isset($this->context[$name])) {
            $value = $this->context[$name];
        }

        return $value;
    }

    /**
     * Returns all available templates.
     *
     * You must overwrite this method for custom message.
     *
     * @return array
     */
    public function getTemplates()
    {
        return [
            self::MODE_AFFIRMATIVE => [
                self::MESSAGE_STANDARD => '{{placeholder}} must be valid',
            ],
            self::MODE_NEGATIVE => [
                self::MESSAGE_STANDARD => '{{placeholder}} must not be valid',
            ],
        ];
    }

    /**
     * Returns the current mode.
     *
     * @return int
     */
    public function getKeyMode()
    {
        return isset($this->context['mode']) ? $this->context['mode'] : self::MODE_AFFIRMATIVE;
    }

    /**
     * Returns the current mode.
     *
     * @return int
     */
    public function getKeyTemplate()
    {
        return isset($this->context['template']) ? $this->context['template'] : self::MESSAGE_STANDARD;
    }

    /**
     * @return string
     */
    private function getMessageTemplate()
    {
        if (isset($this->context['message'])) {
            return $this->context['message'];
        }

        $keyMode = $this->getKeyMode();
        $keyTemplate = $this->getKeyTemplate();
        $templates = $this->getTemplates();

        if (isset($templates[$keyMode][$keyTemplate])) {
            return $templates[$keyMode][$keyTemplate];
        }

        return current(current($templates));
    }

    /**
     * @return string
     */
    private function getPlaceholder()
    {
        $placeholder = $this->context['input'];

        if (is_scalar($placeholder)) {
            $placeholder = var_export($placeholder, true);
        }

        if (isset($this->context['label'])) {
            $placeholder = $this->context['label'];
        }

        if (is_array($placeholder)) {
            $placeholder = '`Array`';
        }

        if (is_object($placeholder)) {
            $placeholder = sprintf('`%s`', get_class($placeholder));
        }

        return $placeholder;
    }

    /**
     * @return string
     */
    private function createMessage()
    {
        $context = $this->context;
        $context['placeholder'] = $this->getPlaceholder();
        $template = $this->getMessageTemplate();

        if (isset($context['message_filter_callback'])) {
            $template = call_user_func($context['message_filter_callback'], $template);
        }

        return preg_replace_callback(
            '/{{(\w+)}}/',
            function ($match) use ($context) {
                $value = $match[0];
                if (isset($context[$match[1]])) {
                    $value = $context[$match[1]];
                }

                return $value;
            },
            $template
        );
    }

    /**
     * @return SplObjectStorage
     */
    public function getIterator()
    {
        if (!$this->children instanceof SplObjectStorage) {
            $this->children = isset($this->context['children']) ? $this->context['children'] : new SplObjectStorage();
        }

        return $this->children;
    }

    public function getChildren()
    {
        $childrenExceptions = new SplObjectStorage();

        $exceptionIterator = new RecursiveExceptionIterator($this);
        $iteratorIterator = new RecursiveIteratorIterator($exceptionIterator, RecursiveIteratorIterator::SELF_FIRST);

        $lastDepth = 0;
        $lastDepthOriginal = 0;
        $knownDepths = [];
        foreach ($iteratorIterator as $childException) {
            if ($childException->hasChildren()
                && $childException->getChildren()->count() < 2) {
                continue;
            }

            $currentDepth = $lastDepth;
            $currentDepthOriginal = $iteratorIterator->getDepth() + 1;

            if (isset($knownDepths[$currentDepthOriginal])) {
                $currentDepth = $knownDepths[$currentDepthOriginal];
            } elseif ($currentDepthOriginal > $lastDepthOriginal) {
                $currentDepth++;
            }

            if (!isset($knownDepths[$currentDepthOriginal])) {
                $knownDepths[$currentDepthOriginal] = $currentDepth;
            }

            $lastDepth = $currentDepth;
            $lastDepthOriginal = $currentDepthOriginal;

            $childrenExceptions->attach(
                $childException,
                [
                    'depth' => $currentDepth,
                    'depth_original' => $currentDepthOriginal,
                    'previous_depth' => $lastDepth,
                    'previous_depth_original' => $lastDepthOriginal,
                ]
            );
        }

        return $childrenExceptions;
    }

    public function hasChildren()
    {
        return ($this->getIterator()->count() > 0);
    }

    /**
     * @return string
     */
    public function getFullMessage()
    {
        $marker = '-';
        $children = $this->getChildren();
        $messages = [sprintf('%s %s', $marker, $this->getMessage())];
        foreach ($children as $exception) {
            $depth = $children[$exception]['depth'];
            $prefix = str_repeat(' ', $depth * 2);
            $messages[] .= sprintf('%s%s %s', $prefix, $marker, $exception->getMessage());
        }

        return implode(PHP_EOL, $messages);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        $messages = [$this->getMessage()];
        foreach ($this->getChildren() as $exception) {
            $messages[] = $exception->getMessage();
        }

        return $messages;
    }
}
