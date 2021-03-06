<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class ScalarDocBlockTypedStub
{
    /** @var int|null */
    private $int = null;

    public function __construct()
    {
        throw new \Exception('Constructor called');
    }

    public function getInt(): int
    {
        return $this->int;
    }
}
