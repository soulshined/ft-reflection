<?php

use FT\Reflection\ClassCache;
use FT\Reflection\ReflectionUtils;
use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/../vendor/autoload.php';

final class ReflectionUtilsTest extends TestCase {

    /**
    * @test
    * @dataProvider class_name_args
    */
    public function should_get_class_name_test($value, $expected) {
        $this->assertEquals($expected, ReflectionUtils::get_class_name($value));
    }

    /**
    * @test
    */
    public function should_get_anonymous_class_test() {
        $anon = new class {};
        $c = ClassCache::get($anon::class);
        $this->assertNotNull($c);
    }

    public function class_name_args() {
        return [
            [new stdClass, 'stdClass'],
            [[], null],
            [(object) [], 'stdClass'],
            [1, null],
            ["string", null],
            [1.9999, null],
            [true, null],
            [false, null],
            [null, null],
            [fn ($i) => false, 'Closure'],
            [function($i) { return; }, 'Closure'],
            [new C, 'C'],
            [new MyClassAttribute, 'MyClassAttribute']
        ];
    }

}