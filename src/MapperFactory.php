<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper;

use Emul\ArrayToClassMapper\DocBlock\DocBlockParser;

class MapperFactory
{
    public function getMapper(): Mapper
    {
        return new Mapper(new DocBlockParser());
    }
}
