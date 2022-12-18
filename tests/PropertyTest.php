<?php

use FT\Reflection\Type;
use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/./classes.php';

final class PropertyTest extends TestCase {

    /**
    * @test
    */
    public function should_get_property_test() {
        $c = new Type(new ReflectionClass(C::class));

        $this->assertNotNull($c->get_property('property'));
        $this->assertNotNull($c->get_property('enumProperty'));
        $this->assertNotNull($c->get_property('staticProperty'));
        $this->assertNull($c->get_property('NAME')); //const
        $this->assertNull($c->get_property('NAME2')); //const
    }

    /**
    * @test
    */
    public function should_get_value_test() {
        $c = new Type(new ReflectionClass(C::class));

        $this->assertEquals('static value', $c->get_property('staticProperty')->get_value());
        $this->assertNull($c->get_property('property')->get_value(new C)); //unitialized property
        $this->assertEquals(12345, $c->get_property('propertyWithDefault')->get_value(new C));
    }

}