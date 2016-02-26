<?php

namespace Respect\Validation\Rules;

use PHPUnit_Framework_TestCase;
use Respect\Validation\ContextInterface;
use Respect\Validation\FactoryInterface;
use Respect\Validation\ResultInterface;

abstract class RuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @return RuleInterface
     */
    protected function getRuleMock()
    {
        return $this->getMock('Respect\\Validation\\Rules\\RuleInterface');
    }

    /**
     * @return FactoryInterface
     */
    protected function getFactoryMock()
    {
        return $this->getMock('Respect\\Validation\\FactoryInterface');
    }

    /**
     * @return ResultInterface
     */
    protected function getResultMock()
    {
        return $this->getMock('Respect\\Validation\\ResultInterface');
    }

    /**
     * @return ContextInterface
     */
    protected function getContextMock()
    {
        return $this->getMock('Respect\\Validation\\ContextInterface');
    }
}
