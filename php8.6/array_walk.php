<?php
// array_walk() invokes the callback with less overhead per element.
// https://github.com/php/php-src/pull/20322

function main(): void
{
    $array = range(1, 1_000);
    $callback = static function (int &$value, int $key): void {
        $value += $key;
    };

    $i = 20_000;
    while ($i--) {
        array_walk($array, $callback);
    }

    echo $array[999], PHP_EOL;
}

main();
