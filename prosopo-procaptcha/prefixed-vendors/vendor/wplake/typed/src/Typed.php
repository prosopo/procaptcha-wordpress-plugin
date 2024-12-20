<?php

declare (strict_types=1);
namespace Io\Prosopo\Procaptcha\Vendors\WPLake\Typed;

use DateTime;
use stdClass;
/**
 * This class is marked as final to prevent extension.
 * It allows us to add new public methods in the future.
 *
 * Note: If you need a generic type casting that’s missing, feel free to open a pull request.
 * For specific use cases, consider implementing your own function.
 */
final class Typed
{
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     * @param mixed $default
     *
     * @return mixed
     */
    public static function any($source, $keys = null, $default = null)
    {
        if (null === $keys) {
            return $source;
        }
        if (\false === is_array($keys)) {
            $keys = explode('.', (string) $keys);
        }
        return self::resolveKeys($source, $keys, $default);
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function string($source, $keys = null, string $default = ''): string
    {
        $value = self::any($source, $keys, $default);
        return \true === is_string($value) || \true === is_numeric($value) ? (string) $value : $default;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function stringExtended($source, $keys = null, string $default = ''): string
    {
        $value = self::any($source, $keys, $default);
        if (\true === is_string($value) || \true === is_numeric($value)) {
            return (string) $value;
        }
        if (\true === is_object($value) && \true === method_exists($value, '__toString')) {
            return (string) $value;
        }
        return $default;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function stringOrNull($source, $keys = null): ?string
    {
        $value = self::any($source, $keys);
        return \true === is_string($value) || \true === is_numeric($value) ? (string) $value : null;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function stringExtendedOrNull($source, $keys = null): ?string
    {
        $value = self::any($source, $keys);
        if (\true === is_string($value) || \true === is_numeric($value)) {
            return (string) $value;
        }
        if (\true === is_object($value) && \true === method_exists($value, '__toString')) {
            return (string) $value;
        }
        return null;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function int($source, $keys = null, int $default = 0): int
    {
        $value = self::any($source, $keys, $default);
        return \true === is_numeric($value) ? (int) $value : $default;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function intOrNull($source, $keys = null): ?int
    {
        $value = self::any($source, $keys);
        return \true === is_numeric($value) ? (int) $value : null;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function float($source, $keys = null, float $default = 0.0): float
    {
        $value = self::any($source, $keys, $default);
        return \true === is_numeric($value) ? (float) $value : $default;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function floatOrNull($source, $keys = null): ?float
    {
        $value = self::any($source, $keys);
        return \true === is_numeric($value) ? (float) $value : null;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function bool($source, $keys = null, bool $default = \false): bool
    {
        $value = self::any($source, $keys, $default);
        return \true === is_bool($value) ? $value : $default;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function boolOrNull($source, $keys = null): ?bool
    {
        $value = self::any($source, $keys);
        return \true === is_bool($value) ? $value : null;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     * @param array<int,mixed> $positive
     * @param array<int,mixed> $negative
     */
    public static function boolExtended($source, $keys = null, bool $default = \false, array $positive = [\true, 1, '1', 'on'], array $negative = [\false, 0, '0', 'off']): bool
    {
        $value = self::any($source, $keys, $default);
        if (\true === in_array($value, $positive, \true)) {
            return \true;
        }
        if (\true === in_array($value, $negative, \true)) {
            return \false;
        }
        return $default;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     * @param array<int,mixed> $positive
     * @param array<int,mixed> $negative
     */
    public static function boolExtendedOrNull($source, $keys = null, array $positive = [\true, 1, '1', 'on'], array $negative = [\false, 0, '0', 'off']): ?bool
    {
        $value = self::any($source, $keys);
        if (\true === in_array($value, $positive, \true)) {
            return \true;
        }
        if (\true === in_array($value, $negative, \true)) {
            return \false;
        }
        return null;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     * @param array<int|string,mixed> $default
     *
     * @return array<int|string,mixed>
     */
    public static function array($source, $keys = null, array $default = []): array
    {
        $value = self::any($source, $keys, $default);
        return \true === is_array($value) ? $value : $default;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     *
     * @return array<int|string,mixed>|null
     */
    public static function arrayOrNull($source, $keys = null): ?array
    {
        $value = self::any($source, $keys);
        return \true === is_array($value) ? $value : null;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function object($source, $keys = null, ?object $default = null): object
    {
        $default = null === $default ? new stdClass() : $default;
        $value = self::any($source, $keys, $default);
        return \true === is_object($value) ? $value : $default;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function objectOrNull($source, $keys = null): ?object
    {
        $value = self::any($source, $keys);
        return \true === is_object($value) ? $value : null;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function dateTime($source, $keys = null, ?DateTime $default = null): DateTime
    {
        $default = null === $default ? new DateTime() : $default;
        $value = self::object($source, $keys, $default);
        return \true === $value instanceof DateTime ? $value : $default;
    }
    /**
     * @param mixed $source
     * @param int|string|array<int,int|string>|null $keys
     */
    public static function dateTimeOrNull($source, $keys = null): ?DateTime
    {
        $value = self::any($source, $keys);
        return \true === $value instanceof DateTime ? $value : null;
    }
    /**
     * @param mixed $source
     * @param int|string $key
     * @param mixed $value
     */
    protected static function resolveKey($source, $key, &$value): bool
    {
        if (\true === is_object($source) && \true === isset($source->{$key})) {
            $value = $source->{$key};
            return \true;
        }
        if (\true === is_array($source) && \true === isset($source[$key])) {
            $value = $source[$key];
            return \true;
        }
        return \false;
    }
    /**
     * @param mixed $source
     * @param array<int,int|string> $keys
     * @param mixed $default
     *
     * @return mixed
     */
    protected static function resolveKeys($source, array $keys, $default)
    {
        foreach ($keys as $key) {
            $value = null;
            if (\true === self::resolveKey($source, $key, $value)) {
                $source = $value;
                continue;
            }
            return $default;
        }
        return $source;
    }
}
