<?php

use FT\Reflection\Type;
use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/./classes.php';

final class AttributeTest extends TestCase {

    /**
    * @test
    */
    public function inherited_class_attribute_test() {
        $c = new Type(new ReflectionClass(C::class));

        $this->assertNotNull($c->get_attribute(MyExtendedAttribute::class));
        $this->assertNull($c->get_attribute(MyAttribute::class));

        $attr = $c->get_attribute(MyExtendedAttribute::class);
        $this->assertTrue($attr->is_inheritable);
    }

    /**
    * @test
    */
    public function inherited_method_attribute_test() {
        $c = new Type(new ReflectionClass(C::class));
        // $method = $c->get_method('no_type');

        // $this->assertNotEmpty($method->get_attributes(MyExtendedMethodAttribute::class));

        //test attribute on interface method
        $method = $c->get_method('name');
        $this->assertTrue($method->has_attribute(MyExtendedMethodAttribute::class));
        $this->assertEquals(['a', 'b', 'c', 'd', 'e'], $method->get_attribute(MyExtendedMethodAttribute::class)->getArgument('value'));

        //test attribute on trait method
        $method = $c->get_method('log');
        $this->assertTrue($method->has_attribute(MyExtendedMethodAttribute::class));
        $this->assertEquals(['a', 'b'], $method->get_attribute(MyExtendedMethodAttribute::class)->getArgument('value'));
    }

    /**
    * @test
    */
    public function inherited_property_attributes_test() {
        $c = new Type(new ReflectionClass(C::class));
        $property = $c->get_property('property');

        $this->assertNotNull($property);
        $this->assertTrue($property->has_attribute(MyExtendedPropertyAttribute::class));
        $this->assertEquals(['a','b','c', 'd', 'e'],
            $property->get_attribute(MyExtendedPropertyAttribute::class)->getArgument('value')
        );

        //test attribute on trait property
        $const = $c->get_property('createdAt');
        $this->assertTrue($const->has_attribute(MyExtendedPropertyAttribute::class));
        $this->assertEquals(['a', 'b'], $const->get_attribute(MyExtendedPropertyAttribute::class)->getArgument('value'));
    }

    /**
    * @test
    */
    public function inherited_constant_attributes_test() {
        $c = new Type(new ReflectionClass(C::class));
        $const = $c->get_constant('NAME2');

        $this->assertNotNull($const);
        $this->assertTrue($const->has_attribute(MyExtendedPropertyAttribute::class));

        //test attribute on interface constant
        $const = $c->get_constant('DEFAULT_NAME');
        $this->assertTrue($const->has_attribute(MyExtendedPropertyAttribute::class));
        $this->assertEquals(['a', 'b'], $const->get_attribute(MyExtendedPropertyAttribute::class)->getArgument('value'));
    }

    /**
    * @test
    */
    public function should_merge_inherited_attribute_array_values_test() {
        $c = new Type(new ReflectionClass(C::class));
        $method = $c->get_method('no_type');

        $this->assertEquals(
            ['a', 'b', 'c', 'd', 'e'],
            $method->get_attribute(MyExtendedMethodAttribute::class)->getArgument('value')
        );
    }


}