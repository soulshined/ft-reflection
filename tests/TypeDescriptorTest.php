<?php

use FT\Reflection\TypeDescriptor;
use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ .'/./classes.php';

final class TypeDescriptorTest extends TestCase {

    /**
    * @test
    * @dataProvider good_types
    */
    public function should_have_type(TypeDescriptor $td, $hasType, $isMultitype, $isMixed, $expecteds) {
        $this->assertEquals($td->hasType, $hasType);
        $this->assertEquals($td->isMultitype, $isMultitype);
        $this->assertEquals($td->isMixed, $isMixed);

        $this->assertEquals($td->can_builtin(), $expecteds[0]);
        $this->assertEquals($td->can_enum(), $expecteds[1]);
        $this->assertEquals($td->can_iterable(), $expecteds[2]);
        $this->assertEquals($td->can_literal(), $expecteds[3]);
        $this->assertEquals($td->can_null(), $expecteds[4]);
        $this->assertEquals($td->can_primitive(), $expecteds[5]);
        $this->assertEquals($td->can_scalar(), $expecteds[6]);
    }

    public function good_types() {
        return [
            $this->arg('union', true, true, false, true, false, true, false, true, true, true),
            $this->arg('mixed', true, false, true, true, false, true, true, true, true, true),
            $this->arg('no_type', false, false, false, false, false, false, false, false, false, false),
            $this->arg('never', true, false, false, true, false, false, false, false, true, false),
            $this->arg('relative_class', true, true, false, true, false, false, false, false, true, false),
            $this->arg('void', true, false, false, true, false, false, false, false, true, false),
            $this->arg('callable', true, false, false, true, false, false, false, false, true, false),
            $this->arg('compound', true, true, false, false, false, false, false, false, false, false),
            $this->arg('enum', true, false, false, false, true, false, false, false, false, false),
            $this->arg('backed_enum', true, false, false, false, true, false, false, false, false, false),
        ];
    }

    public function arg($method, $hasType, $isMultitype, $isMixed, $canBuiltin, $canEnum, $canIterable, $canLiteral, $canNull, $canPrimitive, $canScalar)
    {
        $c = new ReflectionClass(C::class);

        $m = $c->getMethod($method);
        $t = new TypeDescriptor($m->getReturnType());

        return [$t, $hasType, $isMultitype, $isMixed, [$canBuiltin, $canEnum, $canIterable, $canLiteral, $canNull, $canPrimitive, $canScalar]];
    }

}