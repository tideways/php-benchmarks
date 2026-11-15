<?php
// Arguments are passed more efficiently to constructors known at compile time,
// e.g. `new self(...)` with named arguments.
// https://github.com/php/php-src/pull/20259

final class MyClass
{
    private function __construct(
        private \Random\Engine $foo,
        public int $bar = 0,
    ) {
    }

    public static function new(int $bar): self
    {
        $engine = new \Random\Engine\Xoshiro256StarStar(seed: 123);

        return new self(foo: $engine, bar: $bar);
    }
}

$sum = 0;
for ($i = 0; $i < 3_000_000; $i++) {
    $sum += MyClass::new($i)->bar;
}

echo $sum, PHP_EOL;
