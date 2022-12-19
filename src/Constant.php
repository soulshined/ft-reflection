<?php

namespace FT\Reflection;

use FT\Reflection\Attributes\PEL;
use ReflectionClassConstant;

class Constant extends AnnotatedMember {

    public readonly mixed $value;
    public readonly bool $isFinal;

    public function __construct(public readonly ReflectionClassConstant $delegate)
    {
        parent::__construct($delegate);
        $value = $delegate->getValue();
        if ($this->has_attribute(PEL::class))
            $value = PEL::eval($value ?? "null");

        $this->value = $value;
        $this->isFinal = $delegate->isFinal();
    }

}

?>