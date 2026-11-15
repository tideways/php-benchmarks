<?php
// Not an improvement: JSON decode errors now carry a location. The tracking was
// moved off the success path again, this checks for a remaining regression.
// https://github.com/php/php-src/pull/20629 https://github.com/php/php-src/pull/22487

$rows = [];
for ($i = 0; $i < 30; $i++) {
    $rows[] = [
        'id' => $i,
        'name' => "item_\u{00e9}\u{2603}_$i",
        'active' => $i % 2 === 0,
        'score' => $i * 1.5 + 0.25,
        'tags' => ['alpha', 'beta', "gamma\ndelta", 'quote"inside'],
        'meta' => ['nested' => ['deep' => ['value' => 'text with "quotes" and \\ backslash and /slash']]],
        'note' => null,
        'big' => 123456789012345,
    ];
}
$json = json_encode(['rows' => $rows, 'count' => 30, 'ok' => true, 'ratio' => 3.14159]);

$count = 0;
$i = 50_000;
while ($i--) {
    $count += count(json_decode($json, true)['rows']);
}

echo $count, PHP_EOL;
