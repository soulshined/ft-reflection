<?php

namespace FT\Reflection;

use FT\Reflection\Attributes\PEL;
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

            $dvalue = $this->delegate->getValue();
            if (!$this->delegate->isInitialized(null)) {
                if ($this->has_attribute(PEL::class))
                    $dvalue = PEL::eval($dvalue ?? "null");
            }
            return $dvalue;
        }

        if (!$this->delegate->isInitialized($instance)) {
            if ($this->delegate->hasDefaultValue()) {
                $value = $this->delegate->getDefaultValue();
                if ($this->has_attribute(PEL::class))
                    $value = PEL::eval($value ?? "null");
                return $value;
            }

            return null;
        }

        return $this->delegate->getValue($instance);
    }

    public function get_qualified_name()
    {
        return $this->delegate->getDeclaringClass()->name . "." . $this->name;
    }

}