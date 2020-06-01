<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\DocBlock\Entity;

class DocblockType
{
    private bool $isSingle;

    private string $name;

    public function __construct(bool $isSingle, string $name)
    {
        $this->isSingle = $isSingle;
        $this->name     = $name;
    }

    public function isSingle(): bool
    {
        return $this->isSingle;
    }

    public function isBuiltIn(): bool
    {
        return in_array(
            $this->name,
            [
                'boolean', 'bool',
                'integer', 'int',
                'double', 'float',
                'string',
                'array'
            ]
        );
    }

    public function getName(): string
    {
        return $this->name;
    }
}
