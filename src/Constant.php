<?php

namespace FT\Reflection;

use ReflectionClassConstant;

final class Constant extends AnnotatedMember {

    public readonly mixed $value;
    public readonly bool $isFinal;

    public function __construct(public readonly ReflectionClassConstant $delegate)
    {
        parent::__construct($delegate);
        $this->value = $delegate->getValue();
        $this->isFinal = $delegate->isFinal();
    }

    public static function new(): callable
    {
        return fn ($i) => new Constant($i);
    }

}

?>