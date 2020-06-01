<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class ScalarStub
{
    private ?int $int = null;

    private ?string $string = null;

    private bool $bool;

    public function __construct()
    {
        throw new \Exception('Constructor called');
    }


    public function getInt(): int
    {
        return $this->int;
    }

    public function getString(): ?string
    {
        return $this->string;
    }

    public function getBool(): bool
    {
        return $this->bool;
    }
}
