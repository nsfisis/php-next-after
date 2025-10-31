<?php

declare(strict_types=1);

namespace Nsfisis\NextAfter;

use function assert;
use function is_infinite;
use function is_nan;
use function pack;
use function unpack;
use const INF;
use const NAN;
use const PHP_INT_SIZE;

final class NextAfter
{
    private function __construct()
    {
    }

    /**
     * Returns the floating-point number adjacent to $x in the direction of $y.
     * If $x equals $y, returns $y.
     *
     * Special Cases:
     *
     * - If either argument is NaN, the result is NaN
     * - If both arguments equal, $y is returned as it is
     * - If $x is minValue() and $y is greater than $x, the result is positive zero
     * - If $x is -minValue() and $y is less than $x, the result is negative zero
     * - If $x is infinity and $y is not, PHP_FLOAT_MAX is returned
     * - If $x is negative infinity and $y is not, -PHP_FLOAT_MAX is returned
     * - If $x is PHP_FLOAT_MAX and $y is infinity, infinity is returned
     * - If $x is -PHP_FLOAT_MAX and $y is negative infinity, negative infinity is returned
     *
     * @param float $x
     *   Starting floating-point number
     * @param float $y
     *   Floating-point number indicating the direction
     * @return float
     *   The floating-point number adjacent to $x in the direction of $y
     * @phpstan-pure
     */
    public static function nextAfter(float $x, float $y): float
    {
        if (is_nan($x) || is_nan($y)) {
            return NAN;
        }
        if ($x === $y) {
            return $y;
        }
        return $x < $y ? self::nextUp($x) : self::nextDown($x);
    }

    /**
     * Returns the floating-point number adjacent to $x in the direction of positive infinity.
     *
     * This is semantically equivalent to nextAfter($x, INF).
     *
     * Special Cases:
     *
     * - If $x is NaN, the result is NaN
     * - If $x is positive infinity, the result is positive infinity
     * - If $x is zero, the result is minValue()
     * - If $x is -minValue(), the result is negative zero
     * - If $x is PHP_FLOAT_MAX, infinity is returned
     *
     * @param float $x
     *   Starting floating-point number
     * @return float
     *   The floating-point number adjacent to $x in the direction of positive infinity
     * @phpstan-pure
     */
    public static function nextUp(float $x): float
    {
        if (is_nan($x)) {
            return NAN;
        }
        if (is_infinite($x) && $x > 0) {
            return INF;
        }
        if ($x === 0.0) {
            return self::minValue();
        }
        $u = self::floatToInt($x);
        return $x > 0.0 ? self::intToFloat($u + 1) : self::intToFloat($u - 1);
    }

    /**
     * Returns the floating-point number adjacent to $x in the direction of negative infinity.
     *
     * This is semantically equivalent to nextAfter($x, -INF).
     *
     * Special Cases:
     *
     * - If $x is NaN, the result is NaN
     * - If $x is negative infinity, the result is negative infinity
     * - If $x is zero, the result is -minValue()
     * - If $x is minValue(), the result is positive zero
     * - If $x is -PHP_FLOAT_MAX, negative infinity is returned
     *
     * @param float $x
     *   Starting floating-point number
     * @return float
     *   The floating-point number adjacent to $x in the direction of negative infinity
     * @phpstan-pure
     */
    public static function nextDown(float $x): float
    {
        if (is_nan($x)) {
            return NAN;
        }
        if (is_infinite($x) && $x < 0) {
            return -INF;
        }
        if ($x === 0.0) {
            return -self::minValue();
        }
        $u = self::floatToInt($x);
        return $x > 0.0 ? self::intToFloat($u - 1) : self::intToFloat($u + 1);
    }

    /**
     * Returns the minimum representable non-zero floating-point number.
     *
     * Note that this is a subnormal number and is not the same as PHP_FLOAT_MIN,
     * which is the smallest *normal* number.
     *
     * @return float
     *   The minimum representable non-zero floating-point number
     * @phpstan-pure
     */
    public static function minValue(): float
    {
        return self::intToFloat(1);
    }

    /**
     * @phpstan-pure
     */
    private static function intToFloat(int $x): float
    {
        return self::unpackFloat64(self::packInt64($x));
    }

    /**
     * @phpstan-pure
     */
    private static function floatToInt(float $x): int
    {
        return self::unpackInt64(self::packFloat64($x));
    }

    /**
     * @phpstan-pure
     */
    private static function unpackFloat64(string $s): float
    {
        assert(PHP_FLOAT_DIG === 15); // @phpstan-ignore-line
        return unpack('d', $s)[1]; // @phpstan-ignore-line
    }

    /**
     * @phpstan-pure
     */
    private static function packInt64(int $x): string
    {
        assert(PHP_INT_SIZE === 8); // @phpstan-ignore-line
        return pack('q', $x);
    }

    /**
     * @phpstan-pure
     */
    private static function packFloat64(float $x): string
    {
        assert(PHP_FLOAT_DIG === 15); // @phpstan-ignore-line
        return pack('d', $x);
    }

    /**
     * @phpstan-pure
     */
    private static function unpackInt64(string $s): int
    {
        assert(PHP_INT_SIZE === 8); // @phpstan-ignore-line
        return unpack('q', $s)[1]; // @phpstan-ignore-line
    }
}
