<?php
// Freed VM stack pages are cached instead of being returned to the allocator.
// A call stack oscillating across a page boundary on a fragmented heap paid a
// full zend_mm_alloc_pages() search on every oscillation.
// https://github.com/php/php-src/pull/23470

function recurse(int $depth): int
{
    return $depth === 0 ? 0 : recurse($depth - 1) + 1;
}

function main(): void
{
    ini_set('memory_limit', '-1');
    gc_disable();

    // Fragment the heap: allocate ~5.5KB blocks and free every other one, leaving
    // only 2-page holes in every chunk, like a long-running process would.
    $blocks = [];
    for ($i = 0; $i < 60_000; $i++) {
        $blocks[] = str_repeat(chr(65 + $i % 26), 5_500);
    }
    for ($i = 0; $i < 60_000; $i += 2) {
        unset($blocks[$i]);
    }

    $sum = 0;
    $i = 4_000;
    while ($i--) {
        $sum += recurse(6_000);
    }

    echo $sum, ' ', count($blocks), PHP_EOL;
}

main();
