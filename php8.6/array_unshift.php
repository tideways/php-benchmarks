<?php
// array_unshift() has a fast path for packed (list) arrays.
// https://github.com/php/php-src/pull/20353

function main(): void
{
    $list = range(1, 100);

    $count = 0;
    $i = 2_000_000;
    while ($i--) {
        $copy = $list;
        $count += array_unshift($copy, 0, -1);
    }

    echo $count, PHP_EOL;
}

main();
