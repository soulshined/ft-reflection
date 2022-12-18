<?php

namespace FT\Reflection;

use ReflectionProperty;

class Property extends AnnotatedMember
{

    public readonly TypeDescriptor $type;

    public function __construct(public readonly ReflectionProperty $delegate)
    {
        parent::__construct($delegate);
        $this->type = new TypeDescriptor($delegate->getType(), $delegate->hasType());
        $this->delegate->setAccessible(true);
    }

    public function get_value(?object $instance = null) {
        if ($instance == null) {
            if (!$this->delegate->isStatic()) return null;

            return $this->delegate->getValue();
        }

        if (!$this->delegate->isInitialized($instance)) {
            if ($this->delegate->hasDefaultValue())
                return $this->delegate->getDefaultValue();

            return null;
        }

        return $this->delegate->getValue($instance);
    }

    public function get_qualified_name()
    {
        return $this->delegate->getDeclaringClass()->name . "." . $this->name;
    }

    public static function new(): callable
    {
        return fn ($i) => new Property($i);
    }
}