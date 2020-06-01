<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper;

use Closure;
use Emul\ArrayToClassMapper\DocBlock\DocBlockParser;
use Emul\ArrayToClassMapper\DocBlock\Entity\DocblockType;
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
        $this->customMappers[$type] = $mapper;
    }

    public function getCustomerMapper(string $type): ?Closure
    {
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
            foreach ($classProperties as $property) {
                if ($key !== $property->getName()) {
                    continue;
                }

                $property->setAccessible(true);

                $docblockType = $this->docBlockParser->getType((string)$property->getDocComment());
                $type         = $property->getType();

                if (empty($type) && empty($docblockType)) {
                    $property->setValue($object, $value);
                }
                elseif (empty($type) && !empty($docblockType)) {

                }
                else {
                    switch (true) {
                        case $type->isBuiltin() && $type->getName() !== 'array':
                        case $type->getName() === 'array' && empty($docblockType):
                            $property->setValue($object, $value);
                            break;

                        case $type->getName() === 'array' && !empty($docblockType):
                            $property->setValue($object, $this->castArray($value, $docblockType));
                            break;

                        case !$type->isBuiltin():
                            $property->setValue($object, $this->castCustom($value, $type->getName()));
                            break;

                        default:
                            throw new \Exception('Unhandled case');
                    }
                }
            }
        }

        return $object;
    }

    private function castByType(bool $isBuiltInType, string $typeName, ?string $docBlockTypeName)
    {
        switch (true) {
            case $isBuiltInType && $typeName !== 'array':
            case $typeName === 'array' && empty($docBlockTypeName):
                $property->setValue($object, $value);
                break;

            case $typeName === 'array' && !empty($docBlockTypeName):
                $property->setValue($object, $this->castArray($value, $docBlockTypeName));
                break;

            case !$isBuiltInType:
                $property->setValue($object, $this->castCustom($value, $typeName));
                break;

            default:
                throw new \Exception('Unhandled case');
        }
    }

    private function castArray(array $array, DocblockType $docblockType): array
    {
        $castedArray = [];
        if ($docblockType->isBuiltIn()) {
            foreach ($array as $item) {
                $castedItem = $item;
                settype($castedItem, $docblockType->getName());

                $castedArray[] = $castedItem;
            }
        }
        else {
            foreach ($array as $item) {
                $castedArray[] = $this->map($item, $docblockType->getName());
            }
        }

        return $castedArray;
    }

    private function castCustom($value, string $typeName)
    {
        $customMapper = $this->getCustomerMapper($typeName);

        if (empty($customMapper)) {
            return $this->map($value, $typeName);
        }
        else {
            return $customMapper($value);
        }
    }
}
