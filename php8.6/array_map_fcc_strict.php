<?php
// Control for array_map_fcc.php: the array_map() to foreach rewrite is disabled
// under strict_types=1, so no gain is expected here.
// https://github.com/php/php-src/pull/23889

declare(strict_types=1);

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
