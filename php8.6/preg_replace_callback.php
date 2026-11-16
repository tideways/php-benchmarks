<?php
// preg_replace_callback() passes the $matches array to the callback without
// holding an extra reference, so modifying $matches no longer copies it.
// https://github.com/php/php-src/pull/21340

function main(): void
{
    $text = str_repeat('lorem ipsum dolor sit amet consectetur adipiscing elit ', 2_000);
    $length = 0;

    $i = 200;
    while ($i--) {
        $result = preg_replace_callback('/(\w)(\w*)/', static function (array $m): string {
            $m[1] = strtoupper($m[1]);

            return $m[1] . $m[2];
        }, $text);
        $length += strlen($result);
    }

    echo $length, PHP_EOL;
}

main();
