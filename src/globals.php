<?php

/**
 * @return mixed first found element or null otherwise
 */
function array_first(callable $predicate, array ...$arrays) {
    foreach ($arrays as $array) {
        foreach ($array as $value) {
            if ($predicate($value) === true) return $value;
        }
    }

    return null;
}