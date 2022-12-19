<?php

namespace FT\Reflection;

use FT\Reflection\Exceptions\ReflectionException;
use ReflectionClass;

final class DescriptorMapping {

    public readonly ReflectionClass $type_class;
    public readonly ReflectionClass $method_class;
    public readonly ReflectionClass $property_class;
    public readonly ReflectionClass $parameter_class;
    public readonly ReflectionClass $constant_class;

    public function __construct(string $type_class = Type::class,
                                string $property_class = Property::class,
                                string $parameter_class = Parameter::class,
                                string $constant_class = Constant::class,
                                string $method_class = Method::class)
    {
        $this->type_class = $this->validate($type_class, Type::class);
        $this->property_class = $this->validate($property_class, Property::class);
        $this->parameter_class = $this->validate($parameter_class, Parameter::class);
        $this->method_class = $this->validate($method_class, Method::class);
        $this->constant_class = $this->validate($constant_class, Constant::class);
    }

    private function validate($source_class, $target_class) : ReflectionClass {
        $rflc = new ReflectionClass($source_class);

        if ($source_class !== $target_class && !is_subclass_of($source_class, $target_class))
            throw new ReflectionException("$source_class must be a subclass of $target_class");

        return $rflc;
    }

    public function hash() : string {
        return hash('md5',
            $this->type_class .
            $this->constant_class .
            $this->property_class .
            $this->parameter_class .
            $this->method_class
        );
    }

}