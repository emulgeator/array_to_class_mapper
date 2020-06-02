<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit;

use Emul\ArrayToClassMapper\DocBlock\DocBlockParser;

class DocBlockParserTest extends TestCaseAbstract
{
    public function testGetTypeWhenEmptyProvided_shouldReturnNull()
    {
        $parser = new DocBlockParser();

        $type = $parser->getType('');

        $this->assertNull($type);
    }

    public function testGetTypeWhenDoesNotContainType_shouldReturnNull()
    {
        $parser = new DocBlockParser();

        $type = $parser->getType('/** @description something */');

        $this->assertNull($type);
    }

    public function builtInTypeProvider(): array
    {
        return [
            ['/** @var bool */', 'bool'],
            ['/** @var int */', 'int'],
            ['/** @var float */', 'float'],
            ['/** @var string */', 'string'],
            ['/** @var array */', 'array'],
        ];
    }

    /**
     * @dataProvider builtInTypeProvider
     */
    public function testGetTypeWhenTypeGiven_shouldReturnProperly(string $docBlock, string $expectedType)
    {
        $parser = new DocBlockParser();

        $type = $parser->getType($docBlock);

        $this->assertSame(true, $type->isBuiltIn());
        $this->assertSame(true, $type->isSingle());
        $this->assertSame(false, $type->isNullable());
        $this->assertSame($expectedType, $type->getName());
    }

    public function nullableProvider(): array
    {
        return [
            'after type' => ['/** @var int|null */', 'int']
        ];
    }

    /**
     * @dataProvider nullableProvider
     */
    public function testGetTypeWhenNullableTypeGiven_shouldReturnProperly(string $docBlock, string $expectedType)
    {
        $parser = new DocBlockParser();

        $type = $parser->getType($docBlock);

        $this->assertSame(true, $type->isBuiltIn());
        $this->assertSame(true, $type->isSingle());
        $this->assertSame(true, $type->isNullable());
        $this->assertSame($expectedType, $type->getName());
    }

    public function testGetTypeWhenTypedArray_shouldParseProperly()
    {
        $parser = new DocBlockParser();

        $type = $parser->getType('
            /** @var int[] */
        ');

        $this->assertSame(true, $type->isBuiltIn());
        $this->assertSame(false, $type->isSingle());
        $this->assertSame(false, $type->isNullable());
        $this->assertSame('int', $type->getName());
    }

    public function testGetTypeWhenMultiLine_shouldParseProperly()
    {
        $parser = new DocBlockParser();

        $type = $parser->getType('
            /**
             * @customProperty value
             * @var bool|null
             */
        ');

        $this->assertSame(true, $type->isBuiltIn());
        $this->assertSame(true, $type->isSingle());
        $this->assertSame(true, $type->isNullable());
        $this->assertSame('bool', $type->getName());
    }
}
