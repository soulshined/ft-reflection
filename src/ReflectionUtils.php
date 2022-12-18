<?php

namespace FT\Reflection;

use stdClass;

define('IS_AT_LEAST_PHP_8', version_compare(PHP_VERSION, '8.0.0') >= 0);

final class ReflectionUtils {

    public final const TEMPORAL_TYPES = [
        'DateTime',
        'DateTimeImmutable',
        'DateInterval',
        'DatePeriod'
    ];

    public final const PRIMITIVE_TYPES = [
        'array',
        'bool',
        'boolean',
        'callable',
        'double',
        'false',
        'float',
        'int',
        'integer',
        'long',
        'mixed',
        'never',
        'null',
        'NULL',
        'object',
        'parent',
        'resource',
        'resource (closed)',
        'self',
        'static',
        'string',
        'true',
        'void',
        'unknown type'
    ];

    public static function is_primitive($value): bool
    {
        return $value === null || static::is_builtin($value);
    }

    public static function is_builtin($value): bool
    {
        if ($value === null) return true;
        if (in_array(gettype($value), self::PRIMITIVE_TYPES) && !is_object($value)) return true;

        $class = static::get_class_name($value);

        $cc = ClassCache::get($class);
        return $cc->delegate->isInternal();
    }

    public static function is_temporal($value) : bool {
        if ($value === null) return false;

        if (gettype($value) === 'object')
            return in_array($value::class, self::TEMPORAL_TYPES);

        return false;
    }

    public static function get_class_name($value): ?string
    {
        if ($value === null) return null;
        if ($value instanceof stdClass) return 'stdClass';

        $type = gettype($value);
        if ($type === 'object') {
            return $value::class;
        }

        return null;
    }

}