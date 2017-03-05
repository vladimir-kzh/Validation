<?php

/*
 * This file is part of Respect/Validation.
 *
 * (c) Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Respect\Validation;

use DateTimeInterface;
use Exception;
use Traversable;

final class Message
{
    /**
     * @var string
     */
    private $ruleName;

    /**
     * @var string
     */
    private $template;

    /**
     * @var mixed
     */
    private $input;

    /**
     * @var array
     */
    private $properties;

    /**
     * Initializes the object.
     *
     * @param string $ruleName
     * @param string $template
     * @param mixed  $input
     * @param array  $properties
     */
    public function __construct(string $ruleName, string $template, $input, array $properties = [])
    {
        $this->ruleName = $ruleName;
        $this->template = $template;
        $this->input = $input;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getRuleName(): string
    {
        return $this->ruleName;
    }

    public function render(string $placeholder = null): string
    {
        $properties = $this->properties + ['placeholder' => $placeholder];
        if (null === $properties['placeholder']) {
            $properties['placeholder'] = $this->stringify($this->input);
        }

        return preg_replace_callback(
            '/{{(\w+)}}/',
            function ($matches) use ($properties) {
                $value = $matches[0];
                if (array_key_exists($matches[1], $properties)) {
                    $value = $properties[$matches[1]];
                }

                if ('placeholder' === $matches[1] && is_string($value)) {
                    return $value;
                }

                return $this->stringify($value);
            },
            $this->template
        );
    }

    public function __toString(): string
    {
        return $this->render();
    }

    private function stringify($raw, int $currentDepth = 0): string
    {
        if (is_object($raw)) {
            return $this->stringifyObject($raw, $currentDepth);
        }

        if (is_array($raw)) {
            return $this->stringifyArray($raw, $currentDepth);
        }

        if (is_float($raw)) {
            return $this->stringifyFloat($raw, $currentDepth);
        }

        if (is_resource($raw)) {
            return $this->stringifyResource($raw, $currentDepth);
        }

        if (is_bool($raw)) {
            return $this->stringifyBool($raw, $currentDepth);
        }

        return json_encode($raw, (JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function stringifyObject($object, int $currentDepth): string
    {
        $nextDepth = $currentDepth + 1;

        if ($object instanceof Traversable) {
            $array = iterator_to_array($object);
            $string = sprintf('[traversable] (%s: %s)', get_class($object), $this->stringify($array, $nextDepth));

            return $this->quoteCode($string, $currentDepth);
        }

        if ($object instanceof DateTimeInterface) {
            return sprintf('"%s"', $object->format('c'));
        }

        if ($object instanceof Exception) {
            $string = $this->stringifyException($object, $nextDepth);

            return $this->quoteCode($string, $currentDepth);
        }

        if (method_exists($object, '__toString')) {
            return $this->stringify($object->__toString(), $nextDepth);
        }

        $string = sprintf(
            '[object] (%s: %s)',
            get_class($object),
            $this->stringify(get_object_vars($object), $currentDepth)
        );

        return $this->quoteCode($string, $currentDepth);
    }

    private function stringifyException(Exception $exception, int $currentDepth): string
    {
        $properties = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => str_replace(getcwd().'/', '', $exception->getFile()).':'.$exception->getLine(),
        ];

        return sprintf('[exception] (%s: %s)', get_class($exception), $this->stringify($properties, $currentDepth));
    }

    private function stringifyArray(array $array, int $currentDepth): string
    {
        if ($currentDepth >= 3) {
            return '...';
        }

        if (empty($array)) {
            return '{ }';
        }

        $string = '';
        $total = count($array);
        $current = 0;
        $nextDepth = $currentDepth + 1;
        foreach ($array as $key => $value) {
            if ($current++ >= 5) {
                $string .= ' ... ';
                break;
            }

            if (!is_int($key)) {
                $string .= sprintf('%s: ', $this->stringify($key, $nextDepth));
            }

            $string .= $this->stringify($value, $nextDepth);

            if ($current !== $total) {
                $string .= ', ';
            }
        }

        return sprintf('{ %s }', $string);
    }

    private function stringifyFloat(float $float, int $currentDepth): string
    {
        if (is_infinite($float)) {
            return $this->quoteCode(($float > 0 ? '' : '-').'INF', $currentDepth);
        }

        if (is_nan($float)) {
            return $this->quoteCode('NaN', $currentDepth);
        }

        return var_export($float, true);
    }

    private function stringifyResource($raw, int $currentDepth): string
    {
        return $this->quoteCode(sprintf('[resource] (%s)', get_resource_type($raw)), $currentDepth);
    }

    private function stringifyBool(bool $raw, int $currentDepth): string
    {
        return $this->quoteCode(var_export($raw, true), $currentDepth);
    }

    private function quoteCode(string $string, $currentDepth): string
    {
        if (0 === $currentDepth) {
            $string = sprintf('`%s`', $string);
        }

        return $string;
    }
}
