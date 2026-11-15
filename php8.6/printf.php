<?php
// printf() with only %s and %d is compiled into string interpolation, like
// sprintf() since PHP 8.4. https://github.com/php/php-src/pull/19658

$a = 'foo';
$b = 42;

ob_start();
for ($i = 0; $i < 20_000_000; $i++) {
    printf('%s-%d', $a, $b);
    ob_clean();
}
ob_end_clean();

echo 'done', PHP_EOL;
