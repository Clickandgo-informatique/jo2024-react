<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testAssertTrue()
    {
        $this->assertTrue(true);
    }
    public function testAssertEquals()
    {
        $this->assertEquals(5, 2 + 3);
    }
}
