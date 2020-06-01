<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class ArrayStub
{
    private array $array;

    /** @var int[] */
    private array $typedArray;

    /** @var \Emul\ArrayToClassMapper\Test\Unit\Stub\ScalarStub[] */
    private array $scalarArray;

    public function getArray(): array
    {
        return $this->array;
    }

    public function getTypedArray(): array
    {
        return $this->typedArray;
    }

    public function getScalarArray(): array
    {
        return $this->scalarArray;
    }
}
