<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Respect\Validation;

use OutOfBoundsException;
use Respect\Validation\Rules\RuleInterface;
use SplObjectStorage;

interface ResultInterface
{
    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @param bool $isValid
     */
    public function setValid(bool $isValid);

    /**
     * @return RuleInterface
     */
    public function getRule(): RuleInterface;

    /**
     * @return mixed
     */
    public function getInput();

    /**
     * @return array
     */
    public function getProperties(): array;

    /**
     * @return SplObjectStorage
     */
    public function getChildren(): SplObjectStorage;

    /**
     * @return bool
     */
    public function hasChildren(): bool;

    /**
     * @param string $name
     *
     * @throws OutOfBoundsException
     *
     * @return mixed
     */
    public function getProperty(string $name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasProperty(string $name): bool;

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setProperty(string $name, $value);

    /**
     * @param ResultInterface $parentResult
     */
    public function appendTo(ResultInterface $parentResult);

    /**
     * @param ResultInterface $childResult
     */
    public function appendChild(ResultInterface $childResult);
}
