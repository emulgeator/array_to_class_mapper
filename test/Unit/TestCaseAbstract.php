<?php
declare(strict_types=1);

namespace Emul\ArrayToClassMapper\Test\Unit;

use PHPUnit\Framework\TestCase;

class TestCaseAbstract extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->addToAssertionCount(
            \Mockery::getContainer()->mockery_getExpectationCount()
        );
        \Mockery::close();
    }

}
