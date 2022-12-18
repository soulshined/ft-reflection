<?php

namespace FT\Reflection;

use ReflectionParameter;

final class Parameter extends AnnotatedMember
{

    public int $position;
    public readonly TypeDescriptor $type;
    public readonly bool $isRequired;

    public function __construct(public readonly ReflectionParameter $delegate)
    {
        parent::__construct($delegate);
        $this->position = $delegate->getPosition();
        $this->type = new TypeDescriptor($delegate->getType());
        $this->isRequired = !$delegate->isOptional();
    }

    public static function new(): callable
    {
        return fn ($i) => new Parameter($i);
    }
}

?>