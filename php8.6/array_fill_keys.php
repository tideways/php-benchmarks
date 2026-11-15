<?php
// array_fill_keys() was optimized. https://github.com/php/php-src/pull/20347

$intKeys = range(1, 1_000);
$stringKeys = array_map(static fn (int $v): string => "key_$v", $intKeys);

$count = 0;
$i = 50_000;
while ($i--) {
    $count += count(array_fill_keys($intKeys, 0));
    $count += count(array_fill_keys($stringKeys, null));
}

echo $count, PHP_EOL;
