<?php

$version = getenv('TEST_PHP_VERSION');

$output = [];
exec("$version -v", $output);
$output[] = "\n";
exec("$version ./vendor/bin/phpunit tests -d log_level=Debug", $output);

print_r("\n" . join("\n", $output) . "\n");