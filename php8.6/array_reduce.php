<?php
// array_reduce() no longer separates (copies) an array accumulator on every
// callback invocation, turning the `$carry[...] = ...; return $carry;` pattern
// from O(n²) into O(n). https://github.com/php/php-src/pull/21340

function main(): void
{
    $data = [];
    for ($i = 0; $i < 20_000; $i++) {
        $data[] = substr(hash('crc32b', (string) $i), 0, 4);
    }

    $result = array_reduce($data, static function (array $carry, string $item): array {
        $carry[$item] = ($carry[$item] ?? 0) + 1;

        return $carry;
    }, []);

    echo count($result), PHP_EOL;
}

main();
