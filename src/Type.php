<?php

namespace FT\Reflection;

use FT\Reflection\Exceptions\ReflectionException;
use ReflectionClass;

class Type extends AnnotatedMember
{
    public readonly string $shortname;
    /**
     * @var Property[]
     */
    public readonly array $properties;
    /**
     * @var Method[]
     */
    public readonly array $methods;
    public readonly array $constants;

    public function __construct(public readonly ReflectionClass $delegate, private readonly DescriptorMapping $mappings = new DescriptorMapping() )
    {
        parent::__construct($delegate);
        $this->shortname = $delegate->getShortName();
        $this->properties = $this->get_properties_for_type($delegate);
        $this->methods = $this->get_methods_for_type($delegate);
        $this->constants = $this->get_constants_for_type($delegate);
    }

    public function newInstance(...$args)
    {
        if ($this->delegate->getConstructor() === null)
            return $this->delegate->newInstanceWithoutConstructor();

        if (!$this->delegate->getConstructor()->isPublic())
            throw new ReflectionException($this->name . " does not have a publicly accessible constructor");

        return $this->delegate->newInstanceArgs($args);
    }

    public function get_property(string $name): ?Property
    {
        return array_first(fn ($i) => $i->name === $name, $this->properties);
    }

    public function get_method(string $name) : ?Method {
        return array_first(fn ($i) => $i->name === $name, $this->methods);
    }

    public function get_constant(string $name) : ?Constant {
        return array_first(fn ($i) => $i->name === $name, $this->constants);
    }

    /**
     * @return Property[]
     */
    private function get_properties_for_type(ReflectionClass | bool $class): array
    {
        if (is_bool($class)) return [];

        $this_props = array_map(fn ($i) => $this->mappings->property_class->newInstanceArgs([$i]), $class->getProperties());
        $super_props = $this->get_properties_for_type($class->getParentClass());

        $this_prop_names = array_map(fn ($i) => $i->name, $this_props);
        array_push($this_props, ...array_filter($super_props, fn ($i) => !in_array($i->name, $this_prop_names)));
        return $this_props;
    }

    private function get_methods_for_type(ReflectionClass | bool $class) : array {
        if (is_bool($class)) return [];

        $this_methods = array_map(fn ($i) => $this->mappings->method_class->newInstanceArgs([$i, $this->mappings]), $class->getMethods());
        $super_methods = $this->get_methods_for_type($class->getParentClass());

        $this_method_names = array_map(fn ($i) => $i->name, $this_methods);
        array_push($this_methods, ...array_filter($super_methods, fn ($i) => !in_array($i->name, $this_method_names)));

        return $this_methods;
    }

    private function get_constants_for_type(ReflectionClass | bool $class) : array {
        if (is_bool($class)) return [];

        $this_constants = array_map(fn ($i) => $this->mappings->constant_class->newInstanceArgs([$i]), $class->getReflectionConstants());
        $super_constants = $this->get_constants_for_type($class->getParentClass());

        $this_constant_names = array_map(fn ($i) => $i->name, $this_constants);
        array_push($this_constants, ...array_filter($super_constants, fn ($i) => !in_array($i->name, $this_constant_names)));

        return $this_constants;
    }

}