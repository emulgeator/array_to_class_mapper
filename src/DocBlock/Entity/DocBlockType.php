<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\DocBlock\Entity;

class DocBlockType
{
    private string $name;

    private bool $isSingle;

    private bool $isBuiltIn;

    private ?bool $isNullable;

    public function __construct(string $name, bool $isSingle, bool $isBuiltInt, ?bool $isNullable = null)
    {
        $this->name       = $name;
        $this->isSingle   = $isSingle;
        $this->isBuiltIn  = $isBuiltInt;
        $this->isNullable = $isNullable;
    }

    public function isSingle(): bool
    {
        return $this->isSingle;
    }

    public function isBuiltIn(): bool
    {
        return $this->isBuiltIn;
    }

    public function isNullable(): ?bool
    {
        return $this->isNullable;
    }

    public function getName(): string
    {
        return $this->name;
    }

}
