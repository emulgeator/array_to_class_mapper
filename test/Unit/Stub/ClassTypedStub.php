<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class ClassTypedStub
{
    private ?ScalarTypedStub $object;

    public function getObject(): ?ScalarTypedStub
    {
        return $this->object;
    }
}
