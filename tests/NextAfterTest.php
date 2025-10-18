<?php

declare(strict_types=1);

namespace Nsfisis\NextAfter\Tests;

use Nsfisis\NextAfter\NextAfter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use const INF;
use const NAN;

#[CoversClass(NextAfter::class)]
final class NextAfterTest extends TestCase
{
    #[DataProvider('provideNextAfterCases')]
    public function testNextAfter(float $expected, float $x, float $y): void
    {
        self::assertSameFloat($expected, NextAfter::nextAfter($x, $y));
    }

    #[DataProvider('provideNextUpCases')]
    public function testNextUp(float $expected, float $x): void
    {
        self::assertSameFloat($expected, NextAfter::nextUp($x));
    }

    #[DataProvider('provideNextDownCases')]
    public function testNextDown(float $expected, float $x): void
    {
        self::assertSameFloat($expected, NextAfter::nextDown($x));
    }

    public function testMinValue(): void
    {
        self::assertSameFloat(5.0e-324, NextAfter::minValue());
    }

    /**
     * @return iterable<array{float, float, float}>
     */
    public static function provideNextAfterCases(): iterable
    {
        yield 'both NaN' => [NAN, NAN, NAN];
        yield 'x is NaN' => [NAN, NAN, 1.0];
        yield 'y is NaN' => [NAN, 1.0, NAN];
        yield 'same value' => [5.0, 5.0, 5.0];
        yield 'positive/negative zeros' => [-0.0, 0.0, -0.0];
        yield 'negative/positive zeros' => [0.0, -0.0, 0.0];
        yield 'same infinity' => [INF, INF, INF];
        yield 'same negative infinity' => [-INF, -INF, -INF];
        yield 'x < y (both negative)' => [-4.999999999999999, -5.0, -1.0];
        yield 'x < y (x is negative)' => [-0.9999999999999999, -1.0, 1.0];
        yield 'x < y (both positive)' => [1.0000000000000002, 1.0, 5.0];
        yield 'x > y (both positive)' => [4.999999999999999, 5.0, 1.0];
        yield 'x > y (x is positive)' => [0.9999999999999999, 1.0, -1.0];
        yield 'x > y (both negative)' => [-1.0000000000000002, -1.0, -5.0];
        yield 'infinity to zero' => [PHP_FLOAT_MAX, INF, 0.0];
        yield 'infinity to maximum finite value' => [PHP_FLOAT_MAX, INF, PHP_FLOAT_MAX];
        yield 'negative infinity to zero' => [-PHP_FLOAT_MAX, -INF, 0.0];
        yield 'negative infinity to negative maximum finite value' => [-PHP_FLOAT_MAX, -INF, -PHP_FLOAT_MAX];
        yield 'zero to infinity' => [NextAfter::minValue(), 0.0, INF];
        yield 'zero to negative infinity' => [-NextAfter::minValue(), 0.0, -INF];
        yield 'negative zero to infinity' => [NextAfter::minValue(), -0.0, INF];
        yield 'negative zero to negative infinity' => [-NextAfter::minValue(), -0.0, -INF];
        yield 'maximum finite value to inifinity' => [INF, PHP_FLOAT_MAX, INF];
        yield 'negative maximum finite value to negative inifinity' => [-INF, -PHP_FLOAT_MAX, -INF];
        yield 'minimum positive value to zero' => [0.0, NextAfter::minValue(), 0.0];
        yield 'minimum negative value to negative zero' => [-0.0, -NextAfter::minValue(), -0.0];
    }

    /**
     * @return iterable<array{float, float}>
     */
    public static function provideNextUpCases(): iterable
    {
        yield 'NaN' => [NAN, NAN];
        yield 'positive infinity' => [INF, INF];
        yield 'negative infinity' => [-PHP_FLOAT_MAX, -INF];
        yield 'positive zero' => [NextAfter::minValue(), 0.0];
        yield 'negative zero' => [NextAfter::minValue(), -0.0];
        yield 'positive value' => [1.0000000000000002, 1.0];
        yield 'negative value' => [-0.9999999999999999, -1.0];
        yield 'minimum negative value' => [-0.0, -NextAfter::minValue()];
        yield 'maximum finite value' => [INF, PHP_FLOAT_MAX];
    }

    /**
     * @return iterable<array{float, float}>
     */
    public static function provideNextDownCases(): iterable
    {
        yield 'NaN' => [NAN, NAN];
        yield 'negative infinity' => [-INF, -INF];
        yield 'positive infinity' => [PHP_FLOAT_MAX, INF];
        yield 'positive zero' => [-NextAfter::minValue(), 0.0];
        yield 'negative zero' => [-NextAfter::minValue(), -0.0];
        yield 'positive value' => [0.9999999999999999, 1.0];
        yield 'minimum positive value' => [0.0, NextAfter::minValue()];
        yield 'negative value' => [-1.0000000000000002, -1.0];
        yield 'negative maximum finite value' => [-INF, -PHP_FLOAT_MAX];
    }

    private function assertSameFloat(float $expected, float $actual): void
    {
        if (is_nan($expected)) {
            self::assertNan($actual);
        } elseif (is_infinite($expected)) {
            self::assertInfinite($actual);
            if ($expected > 0) {
                self::assertGreaterThan(0, $actual);
            } else {
                self::assertLessThan(0, $actual);
            }
        } elseif ($expected === 0.0) {
            self::assertSame(0.0, $actual);

            $expectedSign = print_r($expected, true)[0] === '-';
            $actualSign = print_r($actual, true)[0] === '-';
            self::assertSame($expectedSign, $actualSign);
        } else {
            self::assertSame($expected, $actual);
        }
    }
}
