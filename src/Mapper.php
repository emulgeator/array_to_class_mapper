<?php

declare(strict_types=1);

namespace Emul\ArrayToClassMapper;

use Closure;
use Emul\ArrayToClassMapper\DocBlock\DocBlockParser;
use Emul\ArrayToClassMapper\DocBlock\Entity\DocBlockType;
use ReflectionClass;

class Mapper
{
    private DocBlockParser $docBlockParser;

    /** @var Closure[] */
    private $customMappers = [];

    public function __construct(DocBlockParser $docBlockParser)
    {
        $this->docBlockParser = $docBlockParser;
    }

    public function addCustomMapper(string $type, Closure $mapper)
    {
        $type = $this->formatCustomTypeName($type);

        $this->customMappers[$type] = $mapper;
    }

    public function removeSpecialCharacters(string $key): string
    {
        return preg_replace('#[^A-Za-z0-9_]#', '', $key);
    }

    public function getCustomMapper(string $type): ?Closure
    {
        $type = $this->formatCustomTypeName($type);

        return empty($this->customMappers[$type])
            ? null
            : $this->customMappers[$type];
    }

    public function map(array $input, string $className)
    {
        $object          = (new ReflectionClass($className))->newInstanceWithoutConstructor();
        $reflectionClass = (new ReflectionClass($object));
        $classProperties = $reflectionClass->getProperties();

        foreach ($input as $key => $value) {
            $key = $this->removeSpecialCharacters($key);

            foreach ($classProperties as $property) {
                if ($key !== $property->getName()) {
                    continue;
                }

                $property->setAccessible(true);

                $docBlockType = $this->docBlockParser->getType((string)$property->getDocComment());
                $type         = $property->getType();

                if (!empty($type)) {
                    $this->setValueByPhpType($type, $docBlockType, $property, $object, $value);
                } else {
                    $this->setValueByDocBlock($docBlockType, $property, $object, $value);
                }
            }
        }

        return $object;
    }

    private function setCustomValue($object, \ReflectionProperty $property, string $typeName, $value): void
    {
        if (
            $value === null
            && empty($this->getCustomMapper($typeName))
        ) {
            $property->setValue($object, null);
        } else {
            $property->setValue($object, $this->castCustom($value, $typeName));
        }
    }

    private function castArray(array $array, DocBlockType $docblockType): array
    {
        $castedArray = [];
        if ($docblockType->getName() === 'array') {
            $castedArray = $array;
        } elseif ($docblockType->isBuiltIn()) {
            foreach ($array as $item) {
                $castedItem = $item;
                settype($castedItem, $docblockType->getName());

                $castedArray[] = $castedItem;
            }
        } elseif (!$docblockType->isSingle()) {
            foreach ($array as $item) {
                $castedArray[] = $this->castCustom($item, $docblockType->getName());
            }
        } else {
            foreach ($array as $item) {
                $castedArray[] = $this->map($item, $docblockType->getName());
            }
        }

        return $castedArray;
    }

    private function castCustom($value, string $typeName)
    {
        $customMapper = $this->getCustomMapper($typeName);

        if (empty($customMapper)) {
            return $this->map($value, $typeName);
        } else {
            return $customMapper($value);
        }
    }

    private function formatCustomTypeName(string $type): string
    {
        return ltrim($type, '\\');
    }

    private function setValueByPhpType(\ReflectionNamedType $type, ?DocBlockType $docBlockType, \ReflectionProperty $property, $object, $value): void
    {
        if ($type->isBuiltin()) {
            if ($type->getName() === 'array') {
                if (empty($docBlockType)) {
                    $property->setValue($object, $value);
                } else {
                    $valueCasted = is_null($value) ? null : $this->castArray($value, $docBlockType);
                    $property->setValue($object, $valueCasted);
                }
            } else {
                switch ($type->getName()) {
                    case 'bool':
                        $value = Caster::castToBool($value);
                        break;

                    case 'int':
                        $value = Caster::castToInt($value);
                        break;

                    case 'float':
                        $value = Caster::castToInt($value);
                        break;
                }
                $property->setValue($object, $value);
            }
        } else {
            $this->setCustomValue($object, $property, $type->getName(), $value);
        }
    }

    private function setValueByDocBlock(?DocBlockType $docBlockType, \ReflectionProperty $property, $object, $value): void
    {
        if (empty($docBlockType)) {
            $property->setValue($object, $value);
        } else {
            if ($docBlockType->isBuiltIn()) {
                if ($docBlockType->getName() === 'array') {
                    $property->setValue($object, $this->castArray($value, $docBlockType));
                } else {
                    settype($value, $docBlockType->getName());
                    $property->setValue($object, $value);
                }
            } elseif (!$docBlockType->isSingle()) {
                $property->setValue($object, $this->castArray($value, $docBlockType));
            } else {
                $this->setCustomValue($object, $property, $docBlockType->getName(), $value);
            }
        }
    }
}
