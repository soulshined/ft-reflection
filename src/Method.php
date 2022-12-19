<?php

namespace FT\Reflection;

use ReflectionMethod;

class Method extends AnnotatedMember {

    public readonly TypeDescriptor $returnType;
    /**
     * @var Parameter[]
     */
    public readonly array $parameters;

    public function __construct(public readonly ReflectionMethod $delegate, private readonly DescriptorMapping $mappings = new DescriptorMapping())
    {
        parent::__construct($delegate);
        $this->delegate->setAccessible(true);
        $this->returnyType = new TypeDescriptor($delegate->getReturnType(), $delegate->hasReturnType());
        $this->parameters = array_map(fn ($i) => $mappings->parameter_class->newInstanceArgs([$i]), $delegate->getParameters());
    }

    public function get_parameter(string $name) : ?Parameter {
        return array_first(fn ($i) => $i->name === $name, $this->parameters);
    }

    /**
     * @param $instance pass null for static methods
     */
    public function invoke(object | null $instance, ...$args) {
        if ($this->delegate->isStatic())
            return $this->delegate->invoke(null, ...$args);

        return $this->delegate->invoke($instance, ...$args);
    }

}