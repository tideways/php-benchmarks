<?php
// array_sum() and array_product() have a specialized path for integer-only
// arrays. https://github.com/php/php-src/pull/21787

$ints = range(1, 10_000);
$signs = [];
for ($i = 0; $i < 10_000; $i++) {
    $signs[] = $i % 3 === 0 ? -1 : 1;
}

$sum = 0;
$i = 20_000;
while ($i--) {
    $sum += array_sum($ints);
    $sum += array_product($signs);
}

echo $sum, PHP_EOL;
