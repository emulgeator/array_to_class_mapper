<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class CustomDocBlockTypeArrayStub
{
    /** @var \Emul\ArrayToClassMapper\Test\Unit\Stub\ScalarTypedStub[] */
    private array $customArray;

    public function getCustomArray(): array
    {
        return $this->customArray;
    }
}
