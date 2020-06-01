<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\DocBlock;

use Emul\ArrayToClassMapper\DocBlock\Entity\DocblockType;

class DocBlockParser
{
    public function getType(string $docblock): ?DocblockType
    {
        $matches = [];
        preg_match('#@var\s*([^\s]*)#i', $docblock, $matches);

        if (empty($matches)) {
            return null;
        }

        $isSingle = strpos($matches[1], '[]') !== false;
        $name     = str_replace(['[', ']'], '', $matches[1]);

        return new DocblockType($isSingle, $name);
    }
}
