<?php
// str_split() was optimized, especially for a split length of 1.
// https://github.com/php/php-src/pull/20419

$string = str_repeat('abcdefghijklmnopqrstuvwxyz', 40);

$count = 0;
$i = 100_000;
while ($i--) {
    $count += count(str_split($string));
    $count += count(str_split($string, 4));
}

echo $count, PHP_EOL;
