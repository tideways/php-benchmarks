<?php
// Writes to public private(set) / protected(set) properties cache the
// visibility check in the runtime cache instead of resolving it on every write.
// https://github.com/php/php-src/pull/22709

final class PrivateSet
{
    public private(set) int $value = 0;

    public function fill(int $n): void
    {
        for ($i = 0; $i < $n; $i++) {
            $this->value = $i;
        }
    }
}

class ProtectedSetBase
{
    public protected(set) int $value = 0;
}

final class ProtectedSetChild extends ProtectedSetBase
{
    public function fill(int $n): void
    {
        for ($i = 0; $i < $n; $i++) {
            $this->value = $i;
        }
    }
}

function main(): void
{
    $private = new PrivateSet();
    $protected = new ProtectedSetChild();
    $private->fill(20_000_000);
    $protected->fill(20_000_000);

    echo $private->value + $protected->value, PHP_EOL;
}

main();
