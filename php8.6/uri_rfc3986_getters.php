<?php
// Normalizing getters on already-normalized Uri\Rfc3986\Uri instances no longer
// copy and re-normalize the URI. https://github.com/php/php-src/pull/21726

$uri = Uri\Rfc3986\Uri::parse('https://user@example.com:8080/path/to/resource?query=value&x=1#fragment');

$length = 0;
$i = 2_000_000;
while ($i--) {
    $length += strlen($uri->getScheme())
        + strlen($uri->getHost())
        + strlen($uri->getPath())
        + strlen($uri->getQuery())
        + strlen($uri->getFragment())
        + strlen($uri->toString());
}

echo $length, PHP_EOL;
