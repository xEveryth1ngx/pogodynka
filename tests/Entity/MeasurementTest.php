<?php

namespace App\Tests\Entity;

use App\Entity\Measurement;
use PHPUnit\Framework\TestCase;

class MeasurementTest extends TestCase
{
    public function dataGetFahrenheit(): array
    {
        return [
            ['0', 32],
            ['-100', -148],
            ['100', 212],
            ['99', 210.2],
            ['-99', -146.2],
            ['-40', -40],
            ['-27', -16.6],
            ['54', 129.2],
            ['0.5', 32.9],
            ['-5.3', 22.46]
        ];
    }

    /**
     * @dataProvider dataGetFahrenheit
     */
    public function testGetFahrenheit($celsius, $expectedFahrenheit): void
    {
        $measurement = new Measurement();
        $measurement->setCelsius($celsius);

        $this->assertEquals($expectedFahrenheit, round($measurement->getFahrenheit(), 2));
    }
}
