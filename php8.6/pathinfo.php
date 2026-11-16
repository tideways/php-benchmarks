<?php
// pathinfo() with a single PATHINFO_* flag reuses the basename and skips work
// for components that were not requested. https://github.com/php/php-src/pull/23726

function main(): void
{
    $path = '/var/www/project/vendor/package/src/Component/SomeClass.php';

    $length = 0;
    $i = 5_000_000;
    while ($i--) {
        $length += strlen(pathinfo($path, PATHINFO_EXTENSION));
        $length += strlen(pathinfo($path, PATHINFO_FILENAME));
        $length += strlen(pathinfo($path, PATHINFO_BASENAME));
    }

    echo $length, PHP_EOL;
}

main();
