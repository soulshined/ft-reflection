<?php

namespace FT\Reflection;

use FT\Reflection\Exceptions\ReflectionException;
use ReflectionClass;

final class ClassCache
{
    private static array $cache = [];
    private static array $custom_mapping_cache = [];

    public static function get(string $class_name): Type
    {
        if (key_exists($class_name, static::$cache))
            return static::$cache[$class_name];

        $rflc = new ReflectionClass($class_name);
        $t = new Type($rflc);

        static::$cache[$class_name] = $t;
        return $t;
    }

    public static function get_with_mappings(string $class_name, DescriptorMapping $mappings) : Type {
        $hash = $mappings->hash();

        if (!key_exists($class_name, static::$custom_mapping_cache))
            static::$custom_mapping_cache[$class_name] = [];

        if (key_exists($hash, static::$custom_mapping_cache[$class_name]))
            return static::$custom_mapping_cache[$class_name][$hash];

        $rflc = new ReflectionClass($class_name);
        $t = $mappings->type_class->newInstanceArgs([$rflc, $mappings]);

        static::$custom_mapping_cache[$class_name][$hash] = $t;
        return $t;
    }

}

?>