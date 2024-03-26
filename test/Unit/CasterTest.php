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
}
