<?php

namespace FT\Reflection;

abstract class AbstractMember {

    public readonly string $doc_comment;
    public readonly string $name;

    /**
     * @param ReflectionProperty | ReflectionParameter | ReflectionEnum | ReflectionClass | ReflectionMethod | ReflectionFunction | ReflectionClassConstant $delegate
     */
    protected function __construct($delegate)
    {
        if (method_exists($delegate, 'getDocComment'))
            $this->doc_comment = $delegate->getDocComment();
        else $this->doc_comment = "";
        $this->name = $delegate->name;
    }

    public static function IS_PRIVATE() {
        return function ($member) {
            if (method_exists($member->delegate, 'isPrivate'))
                return $member->delegate->isPrivate();
            return false;
        };
    }

    public static function IS_PROTECTED() {
        return function ($member) {
            if (method_exists($member->delegate, 'isProtected'))
                return $member->delegate->isProtected();
            return false;
        };
    }

    public static function IS_PUBLIC(): callable {
        return function ($member) {
            if (method_exists($member->delegate, 'isPublic'))
                return $member->delegate->isPublic();
            return false;
        };
    }

}