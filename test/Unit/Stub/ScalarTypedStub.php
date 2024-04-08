<?php

declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class ScalarTypedStub
{
    private ?int   $int;
    private ?float $float;

    public function __construct()
    {
        throw new \Exception('Constructor called');
    }

    public function getInt(): ?int
    {
        return $this->int;
    }

    public function getFloat(): ?float
    {
        return $this->float;
    }
}
