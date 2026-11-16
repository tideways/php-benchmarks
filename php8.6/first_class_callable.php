<?php
// First-class callables of functions and static methods, e.g. `dummy(...)`,
// are cached instead of allocating a new Closure each time they are evaluated.
// https://github.com/php/php-src/pull/19863 (GH-19754)

function dummy(): void
{
}

function encodeToString(callable $writerFunc): void
{
    $writerFunc();
}

function main(): void
{
    $i = 20_000_000;
    while ($i--) {
        encodeToString(dummy(...));
    }

    echo 'done', PHP_EOL;
}

main();
