<?php

namespace FT\Reflection;

final class TypeDescriptor {

    /**
     * @var \ReflectionNamedType[]
     */
    public readonly array $types;

    public readonly bool $hasType;
    public readonly bool $isMultitype;
    public readonly bool $isMixed;
    public readonly bool $isNever;

    public function __construct(
        public readonly \ReflectionNamedType | \ReflectionUnionType | \ReflectionIntersectionType | null $delegate
    )
    {
        if ($delegate !== null) {
            $types = [];

            if ($delegate instanceof \ReflectionNamedType) {
                $types[] = $delegate;
            } else
                foreach ($delegate->getTypes() as $type)
                    $types[] = $type;

            $this->types = $types;
        }
        else $this->types = [];

        $this->hasType = !empty($this->types);
        $this->isMultitype = $this->hasType && count($this->types) > 1;
        $this->isMixed = $this->has_type('mixed');
        $this->isNever = $this->has_type('never');
    }

    public function is_builtin() : bool {
        return !$this->isMultitype && $this->can_builtin();
    }

    public function can_builtin() : bool {
        return $this->can_relative() || array_first(fn ($i) => $i->isBuiltin(), $this->types) !== null;
    }

    public function is_relative() : bool {
        return !$this->isMultitype && $this->can_relative();
    }

    public function can_relative() : bool {
        return $this->isMixed ||
             $this->has_type('self') ||
            $this->has_type('static') ||
            $this->has_type('parent');
    }

    public function is_null() : bool {
        return !$this->isMultitype && $this->can_null();
    }

    public function can_null() : bool {
        return $this->has_type('null') || array_filter($this->types, fn ($i) => $i->allowsNull());
    }

    public function is_scalar() : bool {
        return !$this->isMultitype && $this->can_scalar();
    }

    public function can_scalar() : bool {
        return $this->has_type('bool') ||
            $this->has_type('int') ||
            $this->has_type('float') ||
            $this->has_type('string') ||
            $this->isMixed;
    }

    public function is_literal() : bool {
        return !$this->isMultitype && $this->can_literal();
    }

    public function can_literal() : bool {
        return $this->isMixed || $this->has_type('true') || $this->has_type('false');
    }

    public function is_iterable(): bool
    {
        return !$this->isMultitype && $this->can_iterable();
    }

    public function can_iterable() : bool {
        return $this->isMixed || $this->has_type('iterable') || $this->has_type('array') || $this->has_type('traversable');
    }

    public function is_primitive() : bool {
        return !$this->isMultitype && $this->can_primitive();
    }

    public function is_bool() : bool {
        return !$this->isMultitype && $this->can_bool();
    }

    public function can_bool() : bool {
        return $this->isMixed ||
            $this->has_type('bool') ||
            $this->can_literal();
    }

    public function can_primitive() : bool {
        return $this->isMixed ||
            $this->can_null() ||
            $this->can_scalar() ||
            $this->can_literal() ||
            $this->can_relative() ||
            $this->has_type('array') ||
            $this->has_type('object') ||
            $this->has_type('resource') ||
            $this->has_type('resource (closed)') ||
            $this->has_type('never') ||
            $this->has_type('void') ||
            $this->has_type('callable');
    }

    public function is_enum() : bool {
        return !$this->isMultitype && $this->can_enum();
    }

    public function can_enum() : bool
    {
        if (!$this->hasType) return false;

        foreach ($this->types as $type) {
            if ($type->isBuiltin()) continue;

            if (enum_exists($type->getName())) return true;
        }

        return false;
    }

    public function is_user_class() : bool {
        return !$this->isMultitype && $this->can_user_class();
    }

    public function can_user_class() : bool {
        foreach ($this->types as $type) {
            if ($type->isBuiltin()) continue;

            if (class_exists($type->getName())) {
                $class = ClassCache::get($type->getName());

                if ($class->delegate->isUserDefined()) return true;
            }
        }

        return false;
    }

    public function is_temporal() : bool {
        return !$this->isMultitype && $this->can_temporal();
    }

    public function can_temporal() : bool {
        return $this->isMixed ||
            $this->has_type('DateTime') ||
            $this->has_type('DateTimeImmutable') ||
            $this->has_type('DateInterval') ||
            $this->has_type('DatePeriod');
    }

    public function has_type(string $name) : bool
    {
        if (!$this->hasType) return false;

        return in_array(strtolower($name), array_map(fn ($i) => strtolower($i->getName()), $this->types));
    }

}