<?php

declare(strict_types=1);

namespace Emul\ArrayToClassMapper;

class Caster
{
    public static function castToBool($input): ?bool
    {
        return is_null($input) ? null : filter_var($input, FILTER_VALIDATE_BOOLEAN);
    }

    public static function castToInt($input): ?int
    {
        $result = (is_null($input) || $input === '') ? null : filter_var($input, FILTER_VALIDATE_INT);

        return $result === false ? null : $result;
    }

    public static function castToFloat($input): ?float
    {
        $result = (is_null($input) || $input === '') ? null : filter_var($input, FILTER_VALIDATE_FLOAT);

        return $result === false ? null : $result;
    }
}
