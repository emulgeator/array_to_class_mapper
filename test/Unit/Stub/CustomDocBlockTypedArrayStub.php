<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class CustomDocBlockTypedArrayStub
{
    /** @var \Emul\ArrayToClassMapper\Test\Unit\Stub\CustomStub[] */
    private array $objectArray;

    public function getObjectArray(): array
    {
        return $this->objectArray;
    }
}
