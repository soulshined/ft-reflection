<?php

namespace FT\Reflection\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
final class PEL
{
    public static function eval(string $value)
    {
        $out = $value;
        while (preg_match("/{{\s*(.+?)\s*}}/", $out, $_, PREG_OFFSET_CAPTURE)) {
            $evaled = $_[1][0];

            try {
                $evaled = (function () use ($_) {
                    return eval("return {$_[1][0]};");
                })();
            } catch (\Throwable $ignored) {}

            $re_evaled = substr($out, 0, $_[0][1])
                . $evaled
                . substr($out, $_[0][1] + strlen($_[0][0]));

            static::resolve_type($re_evaled);

            $out = $re_evaled;
        }
        return $out;
    }

    private static function resolve_type(&$value) {
        if ($value === null) return;

        $lcase = strtolower(trim($value === null ? "null" : $value));

        if (in_array($lcase, ['yes', 'no', 'true', 'false']))
            $value = boolval($value);
        else if (is_numeric($lcase))
            $value = intval($value);
    }
}
