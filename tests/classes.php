<?php

use FT\Reflection\AbstractMember;
use FT\Reflection\AnnotatedMember;
use FT\Reflection\Attributes\Inheritable;

enum MyEnum
{
    case A;
    case B;
}

enum MyBackedEnum: string
{
    case A = 'A';
    case B = 'B';
}

trait LoggerTrait {
    #[MyExtendedPropertyAttribute(['a','b'])]
    private static DateTime $createdAt;
    private string $foobar;

    #[MyExtendedMethodAttribute(['a', 'b'])]
    static function log(string $message)  {
        return $message;
    }
}
interface PartyInterface {

    #[MyExtendedMethodAttribute(['a', 'b', 'c'])]
    static function name(): string;

}

interface PersonInterface extends PartyInterface {
    #[MyExtendedPropertyAttribute(['a', 'b'])]
    public final const DEFAULT_NAME = "John";

    #[MyExtendedMethodAttribute(['d', 'b', 'e'])]
    static function name(): string;
    function age() : int;
}

class A implements PersonInterface {
    use LoggerTrait;

    #[MyExtendedPropertyAttribute(value: ['a', 'b', 'c', 'd'])]
    private mixed $property;
    private static string $staticProperty = "static value";
    public final const NAME = "A";
    #[MyExtendedPropertyAttribute]
    private const NAME2 = "abc123";


    #[MyExtendedMethodAttribute(['a', 'b', 'c', 'd'])]
    protected function no_type()
    {
        return null;
    }

    static function name(): string
    {
        return "foobar";
    }

    function age(): int
    {
        return 9;
    }
}

#[MyExtendedAttribute]
class B extends A
{
    #[MyExtendedPropertyAttribute(value: ['a', 'c', 'e'])]
    private mixed $property;
    private mixed $propertyWithDefault = 12345;

    #[MyExtendedMethodAttribute(['a', 'c', 'e'])]
    protected function no_type()
    {
        return null;
    }
}

class C extends B
{
    private mixed $property;
    private MyEnum $enumProperty;
    private const NAME2 = "abc1234";

    function variadic_param(string ...$strings) {

    }

    function union(object | array $param): object | array | string | float | int | bool | null
    {
        return null;
    }
    function mixed(): mixed
    {
        return null;
    }
    protected function no_type()
    {
        return null;
    }
    function never(): never
    {
    }
    function relative_class(): self | static | parent
    {
        return $this;
    }
    function void(): void
    {
        return;
    }
    function callable(): callable
    {
        return function () {
        };
    }
    function compound(): AnnotatedMember & AbstractMember
    {
        return new AnnotatedMember(new ReflectionClass("stdClass"));
    }
    function enum(): MyEnum
    {
        return MyEnum::A;
    }
    function backed_enum(): MyEnum
    {
        return MyEnum::A;
    }
    function echo(string $echo) {
        return $echo;
    }
}

#[Inheritable]
#[Attribute()]
class MyMethodAttribute {
    public function __construct(public readonly array $value)
    {

    }
}

#[Attribute]
class MyExtendedMethodAttribute extends MyMethodAttribute {
    public function __construct(public readonly array $value)
    {
        parent::__construct($value);
    }
}

#[Inheritable]
#[Attribute]
class MyPropertyAttribute {
    public function __construct(public readonly array $value)
    {

    }
}

#[Attribute]
class MyExtendedPropertyAttribute extends MyPropertyAttribute {
    public function __construct(public readonly array $value)
    {
        parent::__construct($value);
    }
}

#[Attribute]
#[Inheritable]
class MyClassAttribute {

}

#[Attribute]
class MyExtendedAttribute extends MyClassAttribute {

}