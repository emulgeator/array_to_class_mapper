<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

class BoolTypedStub
{
    private ?bool $bool = null;

    /** @var bool|null  */
    private $boolDoc = null;

    public function __construct()
    {
        throw new \Exception('Constructor called');
    }

    public function getBool(): ?bool
    {
        return $this->bool;
    }

    public function getBoolDoc(): ?bool
    {
        return $this->boolDoc;
    }
}
