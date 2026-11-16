<?php
// array_intersect() uses hash-based matching instead of sorting all arrays.
// https://github.com/php/php-src/pull/23019

function main(): void
{
    $a = range(0, 9_999);
    $b = range(5_000, 14_999);
    $strA = array_map(static fn (int $v): string => "value_$v", $a);
    $strB = array_map(static fn (int $v): string => "value_$v", $b);

    $count = 0;
    $i = 100;
    while ($i--) {
        $count += count(array_intersect($a, $b));
        $count += count(array_intersect($strA, $strB));
    }

    echo $count, PHP_EOL;
}

main();
