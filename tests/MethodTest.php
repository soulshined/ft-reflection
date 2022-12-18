<?php

use FT\Reflection\Type;
use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/./classes.php';

final class MethodTest extends TestCase {

    /**
    * @test
    */
    public function should_invoke_test() {
        $c = new Type(new ReflectionClass(C::class));
        $method = $c->get_method('enum');

        $this->assertEquals(MyEnum::A, $method->invoke(new C));

        $method2 = $c->get_method('echo');
        $this->assertEquals('Polo', $method2->invoke(new C, 'Polo'));
    }

    /**
    * @test
    */
    public function should_get_method_from_trait_test() {
        $c = new Type(new ReflectionClass(C::class));
        $method = $c->get_method('log');

        $this->assertNotNull($method);
        $this->assertNotNull($method->get_attribute(MyExtendedMethodAttribute::class));
        $this->assertEquals('Polo', $method->invoke(new C, 'Polo'));
    }

}