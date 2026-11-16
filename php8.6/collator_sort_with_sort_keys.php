<?php
// php-extensions: intl
// Collator::sortWithSortKeys() sizes its buffers based on the array size
// instead of allocating 2 x 1 MiB on every call.
// https://github.com/php/php-src/pull/23504

function main(): void
{
    $collator = new Collator('de_DE');
    $words = ['Äpfel', 'Birnen', 'apfel', 'Zitrone', 'Öl', 'Ober', 'Banane', 'über'];

    $count = 0;
    $i = 100_000;
    while ($i--) {
        $copy = $words;
        $collator->sortWithSortKeys($copy);
        $count += count($copy);
    }

    echo $count, ' ', implode(',', $copy), PHP_EOL;
}

main();
