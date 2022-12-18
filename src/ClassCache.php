<?php

namespace FT\Reflection;

use ReflectionClass;

final class ClassCache
{
    private static array $cache = [];

    public static function get(string $class_name): Type
    {
        if (key_exists($class_name, static::$cache))
            return static::$cache[$class_name];

        $rflc = new ReflectionClass($class_name);
        $t = new Type($rflc);

        static::$cache[$class_name] = $t;
        return $t;
    }

}

?>