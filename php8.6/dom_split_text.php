<?php
// php-extensions: dom
// DOMText::splitText() is faster and uses less memory.
// https://github.com/php/php-src/pull/20371

$document = new DOMDocument();
$root = $document->appendChild($document->createElement('root'));

$length = 0;
$i = 2_000;
while ($i--) {
    $root->textContent = '';
    $text = $root->appendChild($document->createTextNode(str_repeat('Lorem ipsum dolor sit amet. ', 40)));
    while ($text->length > 8) {
        $text = $text->splitText(8);
    }
    $length += $root->childNodes->length;
}

echo $length, PHP_EOL;
