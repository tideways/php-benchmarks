<?php
// array_map() with a first-class callable is compiled into an equivalent
// foreach loop: no intermediate Closure, no internal->userland call overhead.
// Only without strict_types, see array_map_fcc_strict.php.
// https://github.com/php/php-src/pull/20934

function increment(int $x): int
{
    return $x + 1;
}

function main(): void
{
    $array = range(1, 1_000);
    $sum = 0;

    $i = 20_000;
    while ($i--) {
        $result = array_map(increment(...), $array);
        $sum += $result[999];
    }

    echo $sum, PHP_EOL;
}

main();
