<?php
// intval('0b...', 2) and intval('+0b...', 2) no longer copy the string to strip
// the prefix. https://github.com/php/php-src/commit/0af87e970

$values = ['0b101010', '+0b11110000', '0B1', '0b1111111111111111111111111111111'];

$sum = 0;
$i = 5_000_000;
while ($i--) {
    foreach ($values as $value) {
        $sum += intval($value, 2);
    }
}

echo $sum, PHP_EOL;
