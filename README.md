# php-next-after

![Packagist](https://img.shields.io/packagist/v/nsfisis/next-after)
![GitHub Actions](https://img.shields.io/github/actions/workflow/status/nsfisis/php-next-after/test.yaml)

A PHP library that provides a port of Java's `Math.nextAfter()` family of functions.


## Overview

This library implements IEEE 754 floating-point manipulation functions that return the next representable floating-point number in a given direction:

- `nextAfter($x, $y)` - Returns the adjacent float in the direction of another value
- `nextUp($x)` - Returns the next float toward positive infinity
- `nextDown($x)` - Returns the next float toward negative infinity


## Requirements

- PHP 8.3 or higher
- 64-bit integers
- IEEE 754 double-precision floats (binary64)


## Installation

```bash
$ composer require nsfisis/next-after
```


## Exapmles

```php
use Nsfisis\NextAfter\NextAfter;

// Get the next representable float after 1.0.
$next = NextAfter::nextAfter(1.0, 2.0); // => 1.0000000000000002

// Get the next float toward positive infinity.
$up = NextAfter::nextUp(1.0); // => 1.0000000000000002

// Get the next float toward negative infinity.
$down = NextAfter::nextDown(1.0); // => 0.9999999999999999
```


## API Reference

### `nextAfter(float $x, float $y): float`

Returns the floating-point number adjacent to `$x` in the direction of `$y`. If `$x` equals `y`, returns `y`.

#### Special Cases

- If either argument is NaN, the result is NaN
- If both arguments equal, `$y` is returned as it is
- If `$x` is `minValue()` and `$y` is greater than `$x`, the result is positive zero
- If `$x` is `-minValue()` and `$y` is less than `$x`, the result is negative zero
- If `$x` is infinity and `$y` is not, `PHP_FLOAT_MAX` is returned
- If `$x` is negative infinity and `$y` is not, `-PHP_FLOAT_MAX` is returned
- If `$x` is `PHP_FLOAT_MAX` and `$y` is infinity, infinity is returned
- If `$x` is `-PHP_FLOAT_MAX` and `$y` is negative infinity, negative infinity is returned

### `nextUp(float $x): float`

Returns the floating-point number adjacent to `$x` in the direction of positive infinity.

This is semantically equivalent to `nextAfter($x, INF)`.

#### Special Cases

- If `$x` is NaN, the result is NaN
- If `$x` is positive infinity, the result is positive infinity
- If `$x` is zero, the result is `minValue()`
- If `$x` is `-minValue()`, the result is negative zero
- If `$x` is `PHP_FLOAT_MAX`, infinity is returned

### `nextDown(float $x): float`

Returns the floating-point number adjacent to `$x` in the direction of negative infinity.

This is semantically equivalent to `nextAfter($x, -INF)`.

#### Special Cases

- If `$x` is NaN, the result is NaN
- If `$x` is negative infinity, the result is negative infinity
- If `$x` is zero, the result is `-minValue()`
- If `$x` is `minValue()`, the result is positive zero
- If `$x` is `-PHP_FLOAT_MAX`, negative infinity is returned

### `minValue(): float`

Returns the minimum representable non-zero floating-point number. Note that this is a subnormal number and is not the same as `PHP_FLOAT_MIN`, which is the smallest *normal* number.


## License

See the [LICENSE](./LICENSE) file.


## Credits

This library is a PHP port of Java's `java.lang.Math.nextAfter()`, `nextUp()`, and `nextDown()` methods.

Reference: [Java Math Documentation](https://docs.oracle.com/javase/8/docs/api/java/lang/Math.html)
