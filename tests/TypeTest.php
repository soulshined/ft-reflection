<?php

use FT\Reflection\AbstractMember;
use FT\Reflection\ClassCache;
use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/./classes.php';

final class TypeTest extends TestCase {

    /**
    * @test
    */
    public function should_create_new_instance_test() {
        $c = ClassCache::get(MyClass::class);

        $this->assertNotNull($c);

        $new = $c->newInstance(null, 9, "abc", new stdClass, [], false);

        $prop = $c->get_property('param1');
        $this->assertEquals(null, $prop->get_value($new));

        $prop = $c->get_property('param2');
        $this->assertEquals(9, $prop->get_value($new));

        $prop = $c->get_property('param3');
        $this->assertEquals('abc', $prop->get_value($new));

        $prop = $c->get_property('varargs');
        $this->assertEquals([new stdClass, [], false], $prop->get_value($new));
    }

    /**
    * @test
    */
    public function should_throw_for_private_constructor_test() {
        $this->expectExceptionMessage("MyClassPrivateConstructor does not have a publicly accessible constructor");

        $c = ClassCache::get(MyClassPrivateConstructor::class);
        $this->assertNotNull($c);
        $c->newInstance(null, 9, "abc", new stdClass, [], false);
    }

    /**
    * @test
    */
    public function should_throw_for_protected_constructor_test() {
        $this->expectExceptionMessage("MyClassProtectedConstructor does not have a publicly accessible constructor");

        $c = ClassCache::get(MyClassProtectedConstructor::class);
        $this->assertNotNull($c);
        $c->newInstance(null, 9, "abc", new stdClass, [], false);
    }

    /**
    * @test
    */
    public function simple_modifiers_test() {
        $c = ClassCache::get(MyClass::class);

        $protected = array_filter($c->methods, AbstractMember::IS_PROTECTED());
        $this->assertEquals(1, count($protected));

        $private = array_filter($c->methods, AbstractMember::IS_PRIVATE());
        $this->assertEquals(1, count($private));

        $c = ClassCache::get(C::class);
        $public = array_filter($c->methods, AbstractMember::IS_PUBLIC());
        $this->assertEquals(14, count($public));
    }

}


class MyClassProtectedConstructor {

    protected function __construct($param1, int $param2, string $param3, ...$args)
    {
    }
}

class MyClassPrivateConstructor {

    private function __construct($param1, int $param2, string $param3, ...$args)
    {
    }
}

class MyClass {
    public $param1;
    public $param2;
    public $param3;
    public $varargs;

    public function __construct($param1, int $param2, string $param3, ...$args)
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
        $this->param3 = $param3;
        $this->varargs = $args;
    }

    protected function void() {

    }

    private function private() {

    }
}
