<?php
// php-extensions: zip
// ZipArchive::addFromString() no longer copies the string contents.
// https://github.com/php/php-src/pull/20497

function main(): void
{
    $file = tempnam(sys_get_temp_dir(), 'bench');
    $content = str_repeat('0123456789abcdef', 16 * 1024);

    $count = 0;
    $i = 50;
    while ($i--) {
        $zip = new ZipArchive();
        $zip->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        for ($j = 0; $j < 200; $j++) {
            $zip->addFromString("file-$j.txt", $content);
        }
        $count += $zip->numFiles;
        $zip->unchangeAll();
        $zip->close();
    }
    @unlink($file);

    echo $count, PHP_EOL;
}

main();
