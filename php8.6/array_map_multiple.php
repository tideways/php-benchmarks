<?php
// array_map() with multiple arrays avoids per-iteration allocations and
// refcounted copies. https://github.com/php/php-src/commit/79adb01bf

function main(): void
{
    $a = range(1, 1_000);
    $b = range(1_001, 2_000);
    $callback = static fn (int $x, int $y): int => $x + $y;

    $sum = 0;
    $i = 20_000;
    while ($i--) {
        $result = array_map($callback, $a, $b);
        $sum += $result[999];
    }

    echo $sum, PHP_EOL;
}

main();
