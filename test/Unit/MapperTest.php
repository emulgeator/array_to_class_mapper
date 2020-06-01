<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit;

use Carbon\Carbon;
use Emul\ArrayToClassMapper\Mapper;
use Emul\ArrayToClassMapper\DocBlock\DocBlockParser;
use Emul\ArrayToClassMapper\DocBlock\Entity\DocblockType;
use Emul\ArrayToClassMapper\Test\Unit\Stub\ArrayStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\CustomStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\ScalarStub;

class MapperTest extends TestCaseAbstract
{
    private DocBlockParser $docBlockParser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->docBlockParser = \Mockery::mock(DocBlockParser::class);
    }

    public function testMapWhenSimpleValueGiven_shouldMapCorrectly()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('', null);

        $input = [
            'int'    => '1',
            'string' => 'value',
            'bool'   => '1',
        ];

        /** @var ScalarStub $result */
        $result = $mapper->map($input, ScalarStub::class);

        $this->assertSame(1, $result->getInt());
        $this->assertSame('value', $result->getString());
        $this->assertSame(true, $result->getBool());
    }

    public function testMapWhenArrayValueGiven_shouldMapCorrectly()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('', null);

        $input = [
            'array' => ['1', '2'],
        ];

        /** @var ArrayStub $result */
        $result = $mapper->map($input, ArrayStub::class);

        $this->assertSame(['1', '2'], $result->getArray());
    }

    public function testMapWhenScalarArrayValueGivenWithDockBlock_shouldMapCorrectly()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('/** @var int[] */', new DocblockType(false, 'int'));

        $input = [
            'typedArray' => ['1', '2'],
        ];

        /** @var ArrayStub $result */
        $result = $mapper->map($input, ArrayStub::class);

        $this->assertSame([1, 2], $result->getTypedArray());
    }

    public function testMapWhenClassArrayValueGivenWithDockBlock_shouldMapCorrectly()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('', null);
        $this->expectTypeRetrievedFromDocBlock(
            '/** @var \Emul\ArrayToClassMapper\Test\Unit\Stub\ScalarStub[] */',
            new DocblockType(false, '\Emul\ArrayToClassMapper\Test\Unit\Stub\ScalarStub')
        );

        $input = [
            'scalarArray' => [
                ['int' => 1],
                ['int' => 2],
            ],
        ];

        /** @var ArrayStub $result */
        $result      = $mapper->map($input, ArrayStub::class);
        $mappedArray = $result->getScalarArray();

        $this->assertCount(2, $mappedArray);
        $this->assertSame(1, $mappedArray[0]->getInt());
        $this->assertSame(2, $mappedArray[1]->getInt());
    }

    public function testMapWhenCustomMapperGiven_shouldUseGivenLogicToMap()
    {
        $currentTime = '2020-01-01 01:01:01';

        $this->expectTypeRetrievedFromDocBlock('', null);

        $input = [
            'currentTime' => $currentTime,
        ];
        $customMapper = \Closure::fromCallable(function (string $timeString) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $timeString);
        });

        $mapper = $this->getMapper();
        $mapper->addCustomMapper(Carbon::class, $customMapper);

        /** @var CustomStub $result */
        $result = $mapper->map($input, CustomStub::class);

        $this->assertSame($currentTime, $result->getCurrentTime()->toDateTimeString());
    }

    private function expectTypeRetrievedFromDocBlock(string $docBlock, ?DocblockType $expectedResult)
    {
        $this->docBlockParser->shouldReceive('getType')->with($docBlock)->andReturn($expectedResult);
    }

    private function getMapper(): Mapper
    {
        return new Mapper($this->docBlockParser);
    }
}
