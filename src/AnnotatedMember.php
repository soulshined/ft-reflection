<?php

namespace FT\Reflection;

use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

abstract class AnnotatedMember extends AbstractMember
{
    /**
     * @var Attribute[]
     */
    public readonly array $attributes;

    /**
     * @param ReflectionProperty | ReflectionParameter | ReflectionEnum | ReflectionClass | ReflectionMethod | ReflectionFunction | ReflectionClassConstant $delegate
     */
    protected function __construct($delegate)
    {
        parent::__construct($delegate);

        if ($delegate instanceof ReflectionClass)
            $this->attributes = $this->get_and_merge_class_attributes($delegate);
        else if ($delegate instanceof ReflectionMethod || $delegate instanceof ReflectionProperty ||
                 $delegate instanceof ReflectionClassConstant)
            $this->attributes = $this->get_and_merge_member_attributes($delegate);

        else $this->attributes = array_map(fn ($i) => new Attribute($i), $delegate->getAttributes());
    }

    public function has_attribute(string $class): bool
    {
        return $this->get_attribute($class) !== null;
    }

    /**
     * @return Attribute | null
     */
    public function get_attribute(string $class): Attribute | null
    {
        return array_first(fn ($i) => $i->name === $class, $this->attributes);
    }

    /**
     * @return Attribute[]
     */
    public function get_attributes(string $class): array
    {
        return array_filter($this->attributes, fn ($i) => $class === $i->name);
    }

    /**
     * @return Attribute[]
     */
    private function get_and_merge_class_attributes(ReflectionClass | bool $class): array
    {
        if ($class === false) return [];

        /**
         * @var Attribute[]
         */
        $this_attrs = array_map(fn ($i) => new Attribute($i), $class->getAttributes());

        $super_attrs = $this->get_and_merge_class_attributes($class->getParentClass());
        $this->merge_attributes($super_attrs, $this_attrs);
        return $this_attrs;
    }

    private function get_and_merge_member_attributes(\ReflectionMethod | \ReflectionProperty | \ReflectionClassConstant $target) {
        /**
         * @var Attribute[]
         */
        $this_attrs = array_map(fn ($i) => new Attribute($i), $target->getAttributes());
        $super_attrs = [];

        $target_type = preg_split("/Reflection(Class)?/", $target::class, -1, PREG_SPLIT_NO_EMPTY)[0];
        $parent = $target->getDeclaringClass()->getParentClass();

        if ($target instanceof ReflectionMethod) {
            foreach ($target->getDeclaringClass()->getInterfaces() as $intf) {
                if (!$intf->hasMethod($target->name)) {
                    $parent_intf = $intf->getParentClass();
                    while ($parent_intf !== false) {
                        if ($parent_intf->hasMethod($target->name)) {
                            $this->merge_attributes(
                                $this->get_and_merge_member_attributes($parent_intf->getMethod($target->name)),
                                $this_attrs
                            );
                            break;
                        }

                        $parent_intf = $parent_intf->getParentClass();
                    }
                } else {
                    $this->merge_attributes(
                        $this->get_and_merge_member_attributes($intf->getMethod($target->name)),
                        $this_attrs
                    );
                }
            }
        }

        while ($parent !== false) {
            if ($parent->{"has$target_type"}($target->name)) {
                if ($target_type === 'Constant')
                    $target_type = 'ReflectionConstant';

                $super_attrs = $this->get_and_merge_member_attributes(
                    $parent->{"get$target_type"}($target->name)
                );

                break;
            }

            $parent = $parent->getParentClass();
        }

        $this->merge_attributes($super_attrs, $this_attrs);
        return $this_attrs;
    }

    private function merge_attributes(array $source, array &$target) {
        foreach ($source as $attr) {
            if (!$attr->is_inheritable) continue;
            if ($attr->is_repeated) {
                $target[] = $attr;
                continue;
            }

            $target_match = array_first(fn ($i) => $i->name === $attr->name, $target);
            if ($target_match === null) {
                $target[] = $attr;
                continue;
            }

            foreach ($attr->getArguments() as $key => $value) {
                if (is_array($value)) {
                    $farray = [...array_values($value), ...$target_match->getArguments()->{$key}];
                    $target_match->getArguments()->{$key} = array_values(array_unique($farray));
                }
            }
        }
    }

}
