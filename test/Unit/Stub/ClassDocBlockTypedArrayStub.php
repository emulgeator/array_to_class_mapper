<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class ClassDocBlockTypedArrayStub
{
    /** @var \Emul\ArrayToClassMapper\Test\Unit\Stub\ScalarTypedStub[] */
    private $objectArray = [];

    public function getObjectArray(): array
    {
        return $this->objectArray;
    }
}
