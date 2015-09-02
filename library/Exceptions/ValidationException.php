<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Exceptions;

use DateTime;
use InvalidArgumentException;
use IteratorAggregate;
use Respect\Validation\Context;
use SplObjectStorage;

class ValidationException extends InvalidArgumentException implements ExceptionInterface, IteratorAggregate
{
    const MESSAGE_STANDARD = 0;

    const MODE_AFFIRMATIVE = 1;
    const MODE_NEGATIVE = 0;

    const DEFAULT_MAX_DEEPT = 5;
    const DEFAULT_MAX_COUNT = 10;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var SplObjectStorage
     */
    private $children;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->message = $this->createMessage();
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
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
        return $this->context->mode;
    }

    /**
     * Returns the current mode.
     *
     * @return int
     */
    public function getKeyTemplate()
    {
        return $this->context->template ?: self::MESSAGE_STANDARD;
    }

    /**
     * @return string
     */
    private function getMessageTemplate()
    {
        if ($this->context->message) {
            return $this->context->message;
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
        if ($this->context->label) {
            return $this->context->label;
        }

        if ($this->context->placeholder) {
            return $this->context->placeholder;
        }

        return $this->normalize($this->context->input);
    }

    /**
     * @param mixed $raw
     * @param int   $deep
     *
     * @return string
     */
    private function normalizeObject($raw, $deep = 2)
    {
        $nextDeep = $deep + 1;

        if ($raw instanceof Traversable) {
            return sprintf('`[traversable] (%s: %s)`', get_class($raw), $this->normalize(iterator_to_array($raw), $nextDeep));
        }

        if ($raw instanceof DateTime) {
            return sprintf('"%s"', $raw->format('c'));
        }

        $class = get_class($raw);

        if ($raw instanceof Exception) {
            $properties = [
                'message' => $raw->getMessage(),
                'code' => $raw->getCode(),
                'file' => $raw->getFile().':'.$raw->getLine(),
            ];

            return sprintf('`[exception] (%s: %s)`', $class, $this->normalize($properties, $nextDeep));
        }

        if (method_exists($raw, '__toString')) {
            return $this->normalize($raw->__toString(), $nextDeep);
        }

        return sprintf('`[object] (%s: %s)`', $class, $this->normalize(get_object_vars($raw), $nextDeep));
    }

    /**
     * @param array $raw
     * @param int   $deep
     *
     * @return string
     */
    private function normalizeArray(array $raw, $deep = 1)
    {
        $nextDeep = $deep + 1;

        if ($nextDeep >= self::DEFAULT_MAX_DEEPT) {
            return '...';
        }

        if (empty($raw)) {
            return '{ }';
        }

        $string = '';
        $total = count($raw);
        $current = 0;
        foreach ($raw as $key => $value) {
            if ($current++ >= self::DEFAULT_MAX_COUNT) {
                $string .= ' ... ';
                break;
            }

            if (!is_int($key)) {
                $string .= sprintf('%s: ', $this->normalize($key, $nextDeep));
            }

            $string .= $this->normalize($value, $nextDeep);

            if ($current !== $total) {
                $string .= ', ';
            }
        }

        return sprintf('{ %s }', $string);
    }

    /**
     * @param mixed $raw
     * @param int   $deep
     *
     * @return string
     */
    private function normalize($raw, $deep = 1)
    {
        if ($deep >= self::DEFAULT_MAX_DEEPT) {
            return '...';
        }

        if (is_object($raw)) {
            return $this->normalizeObject($raw, $deep);
        }

        if (is_array($raw)) {
            return $this->normalizeArray($raw, $deep);
        }

        if (is_resource($raw)) {
            return sprintf('`[resource] (%s)`', get_resource_type($raw));
        }

        if (is_float($raw)) {
            if (is_infinite($raw)) {
                return ($raw > 0 ? '' : '-').'INF';
            }

            if (is_nan($raw)) {
                return 'NaN';
            }
        }

        return json_encode($raw, (JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * @return string
     */
    private function createMessage()
    {
        $params = $this->context->getProperties();
        $params['placeholder'] = $this->getPlaceholder();
        $template = $this->getMessageTemplate();

        if (isset($params['translator'])) {
            $template = call_user_func($params['translator'], $template);
        }

        return preg_replace_callback(
            '/{{(\w+)}}/',
            function ($match) use ($params) {
                $value = $match[0];
                if (isset($params[$match[1]])) {
                    $value = $params[$match[1]];
                }

                if (!is_scalar($value)) {
                    return $this->normalize($value);
                }

                return $value;
            },
            $template
        );
    }

    /**
     * @return Factory
     */
    private function getFactory()
    {
        return $this->context->getFactory();
    }

    /**
     * @return SplObjectStorage
     */
    public function getIterator()
    {
        if (!$this->children instanceof SplObjectStorage) {
            $this->children = $this->getFactory()->createChildrenExceptions($this->getContext());
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
        foreach ($this->getIterator() as $exception) {
            $messages[] = $exception->getMessage();
        }

        return $messages;
    }
}
