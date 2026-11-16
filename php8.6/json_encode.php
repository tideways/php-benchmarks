<?php
// json_encode() is faster for arrays and objects, and JSON_PRETTY_PRINT
// generates indentation faster. https://github.com/php/php-src/pull/21474

function main(): void
{
    $data = [];
    for ($i = 0; $i < 200; $i++) {
        $data[] = [
            'id' => $i,
            'name' => "item $i",
            'tags' => ['alpha', 'beta', 'gamma'],
            'meta' => ['nested' => ['deep' => ['deeper' => ['value' => $i, 'flag' => true]]]],
            'object' => (object) ['a' => 1, 'b' => [1, 2, 3]],
        ];
    }

    $length = 0;
    $i = 5_000;
    while ($i--) {
        $length += strlen(json_encode($data));
        $length += strlen(json_encode($data, JSON_PRETTY_PRINT));
    }

    echo $length, PHP_EOL;
}

main();
