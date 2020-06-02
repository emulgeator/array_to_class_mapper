<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit;

use Carbon\Carbon;
use Emul\ArrayToClassMapper\Mapper;
use Emul\ArrayToClassMapper\DocBlock\DocBlockParser;
use Emul\ArrayToClassMapper\DocBlock\Entity\DocBlockType;
use Emul\ArrayToClassMapper\Test\Unit\Stub\CustomDocBlockTypeArrayStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\CustomDocBlockTypedStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\CustomTypedStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\ScalarDocBlockTypeArrayStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\ScalarDocBlockTypedStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\ScalarTypedStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\TypelessArrayStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\TypelessStub;

class MapperTest extends TestCaseAbstract
{
    private DocBlockParser $docBlockParser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->docBlockParser = \Mockery::mock(DocBlockParser::class);
    }

    public function testMapWhenBuiltInTypedPropertyGiven_shouldCast()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('', null);

        $input = ['int' => '1'];

        /** @var ScalarTypedStub $result */
        $result = $mapper->map($input, ScalarTypedStub::class);

        $this->assertSame(1, $result->getInt());
    }

    public function testMapWhenArrayTypedPropertyGivenWithoutDocBlock_shouldCastElements()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('', null);

        $input = [
            'array' => ['1', '2'],
        ];

        /** @var TypelessArrayStub $result */
        $result = $mapper->map($input, TypelessArrayStub::class);

        $this->assertSame(['1', '2'], $result->getArray());
    }

    public function testMapWhenArrayTypedPropertyGivenWithBuiltInDockBlockType_shouldCastElements()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('/** @var int[] */', new DocBlockType('int', false, true, false));

        $input = [
            'scalarTypedArray' => ['1', '2'],
        ];

        /** @var ScalarDocBlockTypeArrayStub $result */
        $result = $mapper->map($input, ScalarDocBlockTypeArrayStub::class);

        $this->assertSame([1, 2], $result->getScalarTypedArray());
    }

    public function testMapWhenArrayTypedPropertyGivenWithCustomDockBlockType_shouldMapElementsToCustomType()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('', null);
        $this->expectTypeRetrievedFromDocBlock(
            '/** @var \Emul\ArrayToClassMapper\Test\Unit\Stub\ScalarTypedStub[] */',
            new DocBlockType('\Emul\ArrayToClassMapper\Test\Unit\Stub\ScalarTypedStub', false, false, false)
        );

        $input = [
            'customArray' => [
                ['int' => 1],
                ['int' => 2],
            ],
        ];

        /** @var CustomDocBlockTypeArrayStub $result */
        $result      = $mapper->map($input, CustomDocBlockTypeArrayStub::class);
        $mappedArray = $result->getCustomArray();

        $this->assertCount(2, $mappedArray);
        $this->assertSame(1, $mappedArray[0]->getInt());
        $this->assertSame(2, $mappedArray[1]->getInt());
    }

    public function testMapWhenArrayTypedPropertyGivenWithCustomDockBlockTypeAndCustomMapperProvided_shouldMapElementsWithGivenMapper()
    {
        $currentTime = '2020-01-01 01:01:01';

        $this->expectTypeRetrievedFromDocBlock('', null);

        $input = ['currentTime' => $currentTime];
        $customMapper = \Closure::fromCallable(function (string $timeString) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $timeString);
        });

        $mapper = $this->getMapper();
        $mapper->addCustomMapper(Carbon::class, $customMapper);

        /** @var CustomTypedStub $result */
        $result = $mapper->map($input, CustomTypedStub::class);

        $this->assertSame($currentTime, $result->getCurrentTime()->toDateTimeString());
    }

    public function testMapWhenTypelessPropertyGiven_shouldJustStore()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('', null);

        $input = ['property' => 'value'];

        /** @var TypelessStub $result */
        $result = $mapper->map($input, TypelessStub::class);

        $this->assertSame('value', $result->getProperty());
    }

    public function testMapWhenScalarDocBlockTypePropertyGiven_shouldCastToDocumentedType()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('/** @var int|null */', new DocBlockType('int', true, true, true));

        $input = ['int' => '1'];

        /** @var ScalarDocBlockTypedStub $result */
        $result = $mapper->map($input, ScalarDocBlockTypedStub::class);

        $this->assertSame(1, $result->getInt());
    }

    public function testMapWhenCustomDocBlockTypedPropertyGiven_shouldMapWithGivenMapper()
    {
        $currentTime = '2020-01-01 01:01:01';

        $this->expectTypeRetrievedFromDocBlock('/** @var \Carbon\Carbon */', new DocBlockType('\Carbon\Carbon', true, false, false));

        $input = ['currentTime' => $currentTime];
        $customMapper = \Closure::fromCallable(function (string $timeString) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $timeString);
        });

        $mapper = $this->getMapper();
        $mapper->addCustomMapper(Carbon::class, $customMapper);

        /** @var CustomDocBlockTypedStub $result */
        $result = $mapper->map($input, CustomDocBlockTypedStub::class);

        $this->assertSame($currentTime, $result->getCurrentTime()->toDateTimeString());
    }

    private function expectTypeRetrievedFromDocBlock(string $docBlock, ?DocBlockType $expectedResult)
    {
        $this->docBlockParser->shouldReceive('getType')->with($docBlock)->andReturn($expectedResult);
    }

    private function getMapper(): Mapper
    {
        return new Mapper($this->docBlockParser);
    }
}
