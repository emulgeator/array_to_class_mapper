<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit\Stub;

use Carbon\Carbon;

class CustomStub
{
    private Carbon $currentTime;

    public function getCurrentTime(): Carbon
    {
        return $this->currentTime;
    }
}
