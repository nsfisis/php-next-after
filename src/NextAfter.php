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
        return $x > 0.0 ? self::intToFloat($u + 1) :
        self::intToFloat($u - 1);

    }

    /**
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
        return $x > 0.0 ? self::intToFloat($u - 1) :
        self::intToFloat($u + 1);

    }

    /**
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
