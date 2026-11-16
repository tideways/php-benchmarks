<?php
// php-extensions: mbstring
// mb_str_pad() pads by doubling copies instead of appending the pad string in
// a loop. https://github.com/php/php-src/pull/23667

function main(): void
{
    $length = 0;
    $i = 300_000;
    while ($i--) {
        $length += strlen(mb_str_pad('Grüße', 2_000, '–·', STR_PAD_BOTH, 'UTF-8'));
    }

    echo $length, PHP_EOL;
}

main();
