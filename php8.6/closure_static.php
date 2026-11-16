<?php
// Stateless closure cache: a `static` closure that captures nothing is created
// once and reused instead of allocating a new Closure object on every
// evaluation. Non-static closures are not cached.
// https://wiki.php.net/rfc/closure-optimizations#stateless_closure_caching

function apply(callable $callback, int $value): int
{
    return $callback($value);
}

function main(): void
{
    $sum = 0;
    $i = 10_000_000;
    while ($i--) {
        $sum += apply(static fn (int $x): int => $x + 1, $i);
    }

    echo $sum, PHP_EOL;
}

main();
