<?php
// str_repeat($string, 1) returns the input string without copying it.
// https://github.com/php/php-src/pull/23681

$string = str_repeat('x', 100_000);

$length = 0;
$i = 1_000_000;
while ($i--) {
    $length += strlen(str_repeat($string, 1));
}

echo $length, PHP_EOL;
