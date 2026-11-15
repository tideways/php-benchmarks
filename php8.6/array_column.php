<?php
// array_column() moves key normalization out of the main loop.
// https://github.com/php/php-src/pull/23786

$rows = [];
for ($i = 0; $i < 1_000; $i++) {
    $rows[] = ['id' => $i, 'name' => "name $i", 'email' => "user$i@example.com"];
}

$count = 0;
$i = 20_000;
while ($i--) {
    $count += count(array_column($rows, 'name'));
    $count += count(array_column($rows, 'email', 'id'));
}

echo $count, PHP_EOL;
