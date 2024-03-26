<?php

declare(strict_types=1);

namespace Emul\ArrayToClassMapper;

class Caster
{
    public static function castToBool($input): ?bool
    {
        return is_null($input) ? null : filter_var($input, FILTER_VALIDATE_BOOLEAN);
    }
}
