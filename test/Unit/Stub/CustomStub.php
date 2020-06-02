<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class CustomStub
{
    private string $key;
    private string $value;

    public function __construct(string $prefix, string $key, string $value)
    {
        $this->key   = $prefix . $key;
        $this->value = $prefix . $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
