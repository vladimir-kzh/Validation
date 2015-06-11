<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Exceptions;

use Countable;
use RecursiveIterator;
use SplObjectStorage;

class RecursiveExceptionIterator implements RecursiveIterator, Countable
{
    /**
     * @var SplObjectStorage
     */
    private $children;

    /**
     * @param ValidationException $exception
     */
    public function __construct(ValidationException $exception)
    {
        $this->children = $exception->getIterator();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->children->count();
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        if (!$this->valid()) {
            return false;
        }

        return ($this->current()->children instanceof SplObjectStorage);
    }

    /**
     * @return RecursiveExceptionIterator
     */
    public function getChildren()
    {
        return new static($this->current());
    }

    /**
     * @return Exception
     */
    public function current()
    {
        return $this->children->current();
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->children->key();
    }

    public function next()
    {
        $this->children->next();
    }

    public function rewind()
    {
        $this->children->rewind();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->children->valid();
    }
}
