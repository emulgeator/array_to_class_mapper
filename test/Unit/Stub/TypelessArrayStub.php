<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class TypelessArrayStub
{
    private array $array;

    public function getArray(): array
    {
        return $this->array;
    }
}
