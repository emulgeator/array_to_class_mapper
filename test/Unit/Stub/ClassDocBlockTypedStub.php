<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class ClassDocBlockTypedStub
{
    /** @var ScalarTypedStub|null */
    private $object;

    public function getObject(): ?ScalarTypedStub
    {
        return $this->object;
    }
}
