<?php

use FT\Reflection\Type;
use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/./classes.php';

final class ConstantTest extends TestCase {

    /**
    * @test
    */
    public function should_get_constant_test() {
        $c = new Type(new ReflectionClass(C::class));

        $this->assertNotNull($c->get_constant('NAME'));
        $this->assertNotNull($c->get_constant('NAME2'));
    }

    /**
    * @test
    */
    public function should_get_value_test() {
        $c = new Type(new ReflectionClass(C::class));

        $this->assertEquals('A', $c->get_constant('NAME')->value);
        $this->assertEquals('abc1234', $c->get_constant('NAME2')->value);
    }

    /**
    * @test
    */
    public function should_get_const_from_interface_test() {
        $c = new Type(new ReflectionClass(C::class));

        $this->assertNotNull($c->get_constant('DEFAULT_NAME'));
    }
}