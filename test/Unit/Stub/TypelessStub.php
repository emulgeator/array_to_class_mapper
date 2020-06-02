<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class TypelessStub
{
    private $property;

    public function getProperty()
    {
        return $this->property;
    }
}
