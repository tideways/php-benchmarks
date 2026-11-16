<?php
// addcslashes() generates octal escapes for non-printable characters without
// going through a formatted print. https://github.com/php/php-src/pull/23680

function main(): void
{
    $binary = '';
    for ($i = 0; $i < 2_000; $i++) {
        $binary .= chr($i % 256);
    }

    $length = 0;
    $i = 20_000;
    while ($i--) {
        $length += strlen(addcslashes($binary, "\0..\37!@\177..\377"));
    }

    echo $length, PHP_EOL;
}

main();
