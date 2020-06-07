<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit;

use Carbon\Carbon;
use Emul\ArrayToClassMapper\Mapper;
use Emul\ArrayToClassMapper\DocBlock\DocBlockParser;
use Emul\ArrayToClassMapper\DocBlock\Entity\DocBlockType;
use Emul\ArrayToClassMapper\Test\Unit\Stub\ClassDocBlockTypedArrayStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\ClassTypedStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\CustomDocBlockTypedArrayStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\CustomDocBlockTypedStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\CustomStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\CustomTypedStub;
use Emul\ArrayToClassMapper\Test\Unit\Stub\ScalarDocBlockTypedArrayStub;
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

    public function testMapWhenNullGiven_shouldSet()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('', null);

        $input = ['int' => null];

        /** @var ScalarTypedStub $result */
        $result = $mapper->map($input, ScalarTypedStub::class);

        $this->assertNull($result->getInt());
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

        /** @var ScalarDocBlockTypedArrayStub $result */
        $result = $mapper->map($input, ScalarDocBlockTypedArrayStub::class);

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
            'objectArray' => [
                ['int' => 1],
                ['int' => 2],
            ],
        ];

        /** @var ClassDocBlockTypedArrayStub $result */
        $result      = $mapper->map($input, ClassDocBlockTypedArrayStub::class);
        $mappedArray = $result->getObjectArray();

        $this->assertCount(2, $mappedArray);
        $this->assertSame(1, $mappedArray[0]->getInt());
        $this->assertSame(2, $mappedArray[1]->getInt());
    }

    public function testMapWhenClassTypedPropertyGivenWithNullValue_shouldSetToNull()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('', null);

        $input = ['object' => null];

        /** @var ClassTypedStub $result */
        $result = $mapper->map($input, ClassTypedStub::class);

        $this->assertNull($result->getObject());
    }

    public function testMapWhenClassTypedPropertyGiven_shouldMap()
    {
        $mapper = $this->getMapper();

        $this->expectTypeRetrievedFromDocBlock('', null);

        $input = ['object' => ['int' => 1]];

        /** @var ClassTypedStub $result */
        $result = $mapper->map($input, ClassTypedStub::class);

        $this->assertSame(1, $result->getObject()->getInt());
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

    public function testMapWhenCustomDocBlockTypedArrayPropertyGiven_shouldMapWithGivenMapper()
    {
        $this->expectTypeRetrievedFromDocBlock(
            '/** @var \Emul\ArrayToClassMapper\Test\Unit\Stub\CustomStub[] */',
            new DocBlockType('\Emul\ArrayToClassMapper\Test\Unit\Stub\CustomStub', false, false, false)
        );
        $this->expectTypeRetrievedFromDocBlock('', null);

        $input = [
            'objectArray' => [
                ['key' => 'first', 'value' => '1'],
                ['key' => 'second', 'value' => '2'],
            ]
        ];
        $customMapper = \Closure::fromCallable(function (array $data) {
            return new CustomStub('prefix_', $data['key'], $data['value']);
        });

        $mapper = $this->getMapper();
        $mapper->addCustomMapper(CustomStub::class, $customMapper);

        /** @var CustomDocBlockTypedArrayStub $result */
        $result = $mapper->map($input, CustomDocBlockTypedArrayStub::class);

        $this->assertCount(2, $result->getObjectArray());
        $this->assertInstanceOf(CustomStub::class, $result->getObjectArray()[0]);
        $this->assertInstanceOf(CustomStub::class, $result->getObjectArray()[1]);
        $this->assertSame('prefix_first', $result->getObjectArray()[0]->getKey());
        $this->assertSame('prefix_1', $result->getObjectArray()[0]->getValue());
        $this->assertSame('prefix_second', $result->getObjectArray()[1]->getKey());
        $this->assertSame('prefix_2', $result->getObjectArray()[1]->getValue());
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
