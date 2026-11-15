<?php
// stream_copy_to_stream() from a file to a socket uses sendfile(), file to file
// uses copy_file_range(), instead of a userspace read/write loop.
// https://github.com/php/php-src/pull/20399

$size = 64 * 1024 * 1024;
$file = tempnam(sys_get_temp_dir(), 'bench');
$fp = fopen($file, 'w');
$chunk = random_bytes(1024 * 1024);
for ($i = 0; $i < $size / strlen($chunk); $i++) {
    fwrite($fp, $chunk);
}
fclose($fp);

[$writer, $reader] = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
$rounds = 20;

$pid = pcntl_fork();
if ($pid === 0) {
    fclose($writer);
    $received = 0;
    while (!feof($reader)) {
        $received += strlen(fread($reader, 1024 * 1024));
    }
    exit($received === $size * $rounds ? 0 : 1);
}
fclose($reader);

$copied = 0;
for ($i = 0; $i < $rounds; $i++) {
    $in = fopen($file, 'r');
    $copied += stream_copy_to_stream($in, $writer);
    fclose($in);
}
fclose($writer);
pcntl_waitpid($pid, $status);
unlink($file);

if (pcntl_wexitstatus($status) !== 0) {
    throw new RuntimeException('Reader did not receive all bytes');
}

echo $copied, PHP_EOL;
