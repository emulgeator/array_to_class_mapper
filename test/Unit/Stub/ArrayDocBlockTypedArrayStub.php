<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class ArrayDocBlockTypedArrayStub
{
    /** @var array */
    private $array = [];

    public function getArray(): array
    {
        return $this->array;
    }
}
