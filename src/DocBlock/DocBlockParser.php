<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\DocBlock;

use Emul\ArrayToClassMapper\DocBlock\Entity\DocBlockType;

class DocBlockParser
{
    public function getType(string $docblock): ?DocBlockType
    {
        $matches = [];
        preg_match('#@var\s*([^\s]*)#i', $docblock, $matches);

        if (empty($matches)) {
            return null;
        }

        $isSingle   = strpos($matches[1], '[]') === false;
        $types      = explode('|', str_replace(['[', ']'], '', $matches[1]));
        $isNullable = false;

        foreach ($types as $index => $type) {
            $type = trim($type);
            if ($type === 'null') {
                $isNullable = true;
                unset($types[$index]);
            }
            else {
                $types[$index] = $type;
            }
        }

        $chosenType = reset($types);
        $isBuiltIn = $this->isBuiltInType($chosenType);

        return new DocBlockType($chosenType, $isSingle, $isBuiltIn, $isNullable);
    }

    private function isBuiltInType(string $type): bool
    {
        return in_array(
            $type,
            [
                'bool',
                'int',
                'float',
                'string',
                'array',
            ]
        );
    }
}
