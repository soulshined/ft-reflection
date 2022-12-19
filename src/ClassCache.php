<?php

namespace FT\Reflection;

use ReflectionClass;

final class ClassCache
{
    private static array $cache = [];
    private static array $custom_mapping_cache = [];

    public static function get(string $class_name): Type
    {
        $is_stdClass = static::is_stdClass($class_name);

        if (!$is_stdClass && key_exists($class_name, static::$cache))
            return static::$cache[$class_name];

        $rflc = new ReflectionClass($class_name);
        $t = new Type($rflc);

        if (!$is_stdClass) static::$cache[$class_name] = $t;
        return $t;
    }

    public static function get_with_mappings(string $class_name, DescriptorMapping $mappings) : Type {
        $is_stdClass = static::is_stdClass($class_name);
        $hash = $mappings->hash();

        if (!$is_stdClass && !key_exists($class_name, static::$custom_mapping_cache))
            static::$custom_mapping_cache[$class_name] = [];

        if (!$is_stdClass && key_exists($hash, static::$custom_mapping_cache[$class_name]))
            return static::$custom_mapping_cache[$class_name][$hash];

        $rflc = new ReflectionClass($class_name);
        $t = $mappings->type_class->newInstanceArgs([$rflc, $mappings]);

        if (!$is_stdClass) static::$custom_mapping_cache[$class_name][$hash] = $t;
        return $t;
    }

    private static function is_stdClass(string $class) {
        return strtolower($class) === 'stdclass';
    }

}

?>