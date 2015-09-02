<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Helpers;

use DateTime;
use Exception;
use Traversable;

trait NormalizerHelper
{
    /**
     * @var int
     */
    private $maxDeept = 5;

    /**
     * @var int
     */
    private $maxCount = 10;

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

        if ($nextDeep >= $this->maxDeept) {
            return '...';
        }

        if (empty($raw)) {
            return '{ }';
        }

        $string = '';
        $total = count($raw);
        $current = 0;
        foreach ($raw as $key => $value) {
            if ($current++ >= $this->maxCount) {
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
    public function normalize($raw, $deep = 1)
    {
        if ($deep >= $this->maxDeept) {
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
}
