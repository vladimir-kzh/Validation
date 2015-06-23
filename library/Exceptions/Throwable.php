<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Exceptions;

interface Throwable
{
    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return mixed
     */
    public function getCode();

    /**
     * @return string
     */
    public function getFile();

    /**
     * @return int
     */
    public function getLine();

    /**
     * @return array
     */
    public function getTrace();

    /**
     * @return string
     */
    public function getTraceAsString();

    /**
     * @return string
     */
    public function __toString();
}
