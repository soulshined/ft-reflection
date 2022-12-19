<?php

namespace FT\Reflection;

use FT\Reflection\Attributes\PEL;
use ReflectionParameter;

class Parameter extends AnnotatedMember
{

    public int $position;
    public readonly TypeDescriptor $type;
    public readonly bool $isRequired;
    public readonly mixed $defaultValue;

    public function __construct(public readonly ReflectionParameter $delegate)
    {
        parent::__construct($delegate);
        $this->position = $delegate->getPosition();
        $this->type = new TypeDescriptor($delegate->getType());
        $this->isRequired = !$delegate->isOptional();

        $dvalue = null;
        if ($delegate->isDefaultValueAvailable()) {
            $dvalue = $this->delegate->getDefaultValue();
            if ($this->has_attribute(PEL::class))
                $dvalue = PEL::eval($dvalue ?? "null");
        }

        $this->defaultValue = $dvalue;
    }

}

?>