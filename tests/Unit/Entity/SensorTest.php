<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Sensor;
use PHPUnit\Framework\TestCase;

class SensorTest extends TestCase
{
    public function testKeyCodeGivenAtConstructionIsKept(): void
    {
        $this->assertSame('AAA', (new Sensor('AAA'))->getKeyCode());
    }

    public function testKeyCodeZeroGivenAtConstructionIsKept(): void
    {
        $this->assertSame('0', (new Sensor('0'))->getKeyCode());
    }

    public function testRandomHexadecimalKeyCodeIsGeneratedWhenEmptyStringIsGiven(): void
    {
        $this->assertMatchesRegularExpression('/^[0-9a-f]{16}$/', (new Sensor(''))->getKeyCode());
    }

    public function testRandomHexadecimalKeyCodeIsGeneratedWhenNoneIsGiven(): void
    {
        $first = (new Sensor())->getKeyCode();
        $second = (new Sensor())->getKeyCode();

        $this->assertMatchesRegularExpression('/^[0-9a-f]{16}$/', $first);
        $this->assertNotSame($first, $second);
    }

    public function testNewSensorIsInactiveByDefault(): void
    {
        $this->assertFalse((new Sensor('AAA'))->getActif());
    }
}
