<?php
// Sorting single-element arrays skips unpacking and the comparator entirely.
// https://github.com/php/php-src/pull/23550

$compare = static fn (int $a, int $b): int => $a <=> $b;

$sum = 0;
$i = 5_000_000;
while ($i--) {
    $a = [$i];
    sort($a);
    usort($a, $compare);
    $h = ['k' => $i];
    asort($h);
    $sum += $a[0] + $h['k'];
}

echo $sum, PHP_EOL;
