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

use ArrayIterator;
use DateTime;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @group engine
 *
 * @covers \Respect\Validation\Message\Formatter
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 *
 * @since 2.0.0
 */
final class MessageTest extends TestCase
{
    public function normalizerDataProvider(): array
    {
        $arrayThreeLevels = ['foo' => ['bar' => ['baz' => 'Here!']]];
        $arrayFourLevels = ['foo' => ['bar' => ['baz' => ['qux' => 'Here!']]]];

        return [
            [
                new Exception(),
                '`[exception] (Exception: { "message": "", "code": 0, "file": "tests/library/MessageTest.php:39" })`',
            ],
            [
                $this->arrayToObject($arrayThreeLevels),
                '`[object] (stdClass: { "foo": [object] (stdClass: { "bar": [object] (stdClass: { "baz": "Here!" }) }) })`',
            ],
            [
                $this->arrayToObject($arrayFourLevels),
                '`[object] (stdClass: { "foo": [object] (stdClass: { "bar": [object] (stdClass: { "baz": [object] (stdClass: ...) }) }) })`',
            ],
            [new stdClass(), '`[object] (stdClass: { })`'],
            [new DateTime('2017-03-05T15:20:05+00:00'), '"2017-03-05T15:20:05+00:00"'],
            [new DateTimeImmutable('2017-03-05T15:20:05+00:00'), '"2017-03-05T15:20:05+00:00"'],
            [new ArrayIterator(range(1, 3)), '`[traversable] (ArrayIterator: { 1, 2, 3 })`'],
            [$this->getObjectWithToString(), '"__toString"'],
            [[], '{ }'],
            [range(1, 5), '{ 1, 2, 3, 4, 5 }'],
            [range(1, 9), '{ 1, 2, 3, 4, 5,  ...  }'],
            [$arrayThreeLevels, '{ "foo": { "bar": { "baz": "Here!" } } }'],
            [$arrayFourLevels, '{ "foo": { "bar": { "baz": ... } } }'],
            [tmpfile(), '`[resource] (stream)`'],
            [1.0, '1.0'],
            [INF, '`INF`'],
            [INF * -1, '`-INF`'],
            [acos(8), '`NaN`'],
            [true, '`true`'],
            [false, '`false`'],
            ['Something', '"Something"'],
            ['What "if"', '"What \"if\""'],
            ['What \'if\'', '"What \'if\'"'],
            [42, '42'],
        ];
    }

    private function arrayToObject(array $array): stdClass
    {
        $object = (object) $array;
        foreach ($object as &$property) {
            if (is_array($property)) {
                $property = $this->arrayToObject($property);
            }
        }

        return $object;
    }

    private function getObjectWithToString()
    {
        return new class() {
            public function __toString()
            {
                return __FUNCTION__;
            }
        };
    }

    /**
     * @test
     */
    public function shouldDefineAndRetrieveRuleName(): void
    {
        $ruleName = 'MyRuleName';

        $message = new Message($ruleName, 'template', 'input');

        self::assertSame($ruleName, $message->getRuleName());
    }

    /**
     * @test
     */
    public function shouldUseInputAsPlaceholderWhenNoPlaceholderIsDefined(): void
    {
        $template = 'This is a message with {{placeholder}}';

        $message = new Message('RuleName', $template, 12345);

        $expectedMessage = 'This is a message with 12345';

        self::assertSame($expectedMessage, $message->render());
    }

    /**
     * @test
     */
    public function shouldUsePlaceholderParameterWhenNoPlaceholderKeyIsDefined(): void
    {
        $template = 'This is a message with {{placeholder}}';

        $message = new Message('RuleName', $template, 12345);

        $expectedMessage = 'This is a message with placeholder';

        self::assertSame($expectedMessage, $message->render('placeholder'));
    }

    /**
     * @test
     */
    public function shouldUsePlaceholderKeyAsPlaceholderWhenDefined(): void
    {
        $template = 'This is a message with {{placeholder}}';

        $message = new Message('RuleName', $template, 123, ['placeholder' => 456]);

        $expectedMessage = 'This is a message with 456';

        self::assertSame($expectedMessage, $message->render(789));
    }

    /**
     * @test
     *
     * @dataProvider normalizerDataProvider
     *
     * @param mixed  $input
     * @param string $expectedMessage
     */
    public function shouldNormalizeInput($input, string $expectedMessage): void
    {
        $message = new Message('RuleName', '{{placeholder}}', $input);
        $actualMessage = $message->render();

        self::assertSame($expectedMessage, $actualMessage);
    }

    /**
     * @test
     */
    public function shouldNotNormalizePlaceholderKey(): void
    {
        $template = 'This is a message with {{placeholder}}';

        $message = new Message('RuleName', $template, 12345, ['placeholder' => 'some name']);

        $expectedMessage = 'This is a message with some name';

        self::assertSame($expectedMessage, $message->render());
    }

    /**
     * @test
     */
    public function shouldReplaceAllProperties(): void
    {
        $template = '{{foo}}, {{bar}}, and {{baz}}';
        $properties = ['foo' => 1, 'bar' => 2, 'baz' => 3];

        $message = new Message('RuleName', $template, 'input', $properties);

        $expectedMessage = '1, 2, and 3';

        self::assertSame($expectedMessage, $message->render());
    }

    /**
     * @test
     */
    public function shouldNormalizeAllProperties(): void
    {
        $template = '{{foo}}, {{bar}}, and {{baz}}';
        $properties = ['foo' => true, 'bar' => 'name', 'baz' => new stdClass()];

        $message = new Message('RuleName', $template, 'input', $properties);

        $expectedMessage = '`true`, "name", and `[object] (stdClass: { })`';

        self::assertSame($expectedMessage, $message->render());
    }
}
