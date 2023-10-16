<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class ScalarDocBlockTypedArrayStub
{
    /** @var int[]|null */
    private ?array $scalarTypedArray;

    public function getScalarTypedArray(): ?array
    {
        return $this->scalarTypedArray;
    }

}
