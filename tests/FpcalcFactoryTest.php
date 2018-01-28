<?php

namespace Tests;

use Fpcalc\FpcalcFactory;
use Fpcalc\FpcalcProcess;
use PHPUnit\Framework\TestCase;

class FpcalcFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = new FpcalcFactory();

        $this->assertInstanceOf(FpcalcProcess::class, $factory->create(), 'Must return instance of FpcalcProcess');
    }
}
