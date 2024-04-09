<?php

declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit;

use Emul\ArrayToClassMapper\Caster;

class CasterTest extends TestCaseAbstract
{
    public static function boolProvider(): array
    {
        return [
            'bool true'    => [true, true],
            'bool false'   => [false, false],
            'string true'  => ['true', true],
            'string false' => ['false', false],
            'int true'     => [1, true],
            'int false'    => [0, false],
            'null'         => [null, null],
        ];
    }

    /**
     * @dataProvider boolProvider
     */
    public function testCastToBool($input, ?bool $mappedValue)
    {
        $result = Caster::castToBool($input);

        $this->assertSame($mappedValue, $result);
    }

    public static function intProvider(): array
    {
        return [
            'integer'        => [1, 1],
            'string integer' => ['11', 11],
            'null'           => [null, null],
            'empty string'   => ['', null],
            'zero'           => [0, 0],
            'invalid'        => [false, null],
        ];
    }

    /**
     * @dataProvider intProvider
     */
    public function testCastToInt($input, ?int $mappedValue)
    {
        $result = Caster::castToInt($input);

        $this->assertSame($mappedValue, $result);
    }

    public static function floatProvider(): array
    {
        return [
            'float'            => [1.1, 1.1],
            'string floateger' => ['2.2', 2.2],
            'null'             => [null, null],
            'empty string'     => ['', null],
            'zero'             => [0, 0],
            'invalid'          => [false, null],
        ];
    }

    /**
     * @dataProvider floatProvider
     */
    public function testCastToFloat($input, ?float $mappedValue)
    {
        $result = Caster::castToFloat($input);

        $this->assertSame($mappedValue, $result);
    }
}
