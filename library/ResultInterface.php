<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation;

use OutOfBoundsException;
use Respect\Validation\Rules\RuleInterface;

interface ResultInterface
{
    /**
     * @return bool
     */
    public function isValid();

    /**
     * @param bool $isValid
     */
    public function setValid($isValid);

    /**
     * @return RuleInterface
     */
    public function getRule();

    /**
     * @return ContextInterface
     */
    public function getContext();

    /**
     * @return array
     */
    public function getProperties();

    /**
     * @return Result[]
     */
    public function getChildren();

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @param string $name
     *
     * @throws OutOfBoundsException
     *
     * @return mixed
     */
    public function getProperty($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasProperty($name);

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setProperty($name, $value);

    /**
     * @param ResultInterface $parentResult
     */
    public function appendTo(ResultInterface $parentResult);

    /**
     * @param ResultInterface $childResult
     */
    public function appendChild(ResultInterface $childResult);
}
