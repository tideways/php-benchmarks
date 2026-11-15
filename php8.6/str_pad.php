<?php
// str_pad() fills the padding with memset() for single characters and by
// doubling copies for multi-character pad strings.
// https://github.com/php/php-src/pull/19272 https://github.com/php/php-src/pull/23661

$length = 0;
$i = 500_000;
while ($i--) {
    $length += strlen(str_pad('hello', 2_000, ' ', STR_PAD_RIGHT));
    $length += strlen(str_pad('hello', 2_000, '-=', STR_PAD_LEFT));
}

echo $length, PHP_EOL;
