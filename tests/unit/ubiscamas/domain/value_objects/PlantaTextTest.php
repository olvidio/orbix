<?php

namespace Tests\unit\ubiscamas\domain\value_objects;

use src\ubiscamas\domain\value_objects\PlantaText;
use Tests\myTest;

class PlantaTextTest extends myTest
{
    public function test_create_valid_plantaText()
    {
        $planta = new PlantaText('Primera');
        $this->assertEquals('Primera', $planta->value());
    }

    public function test_to_string_returns_value()
    {
        $planta = new PlantaText('Primera');
        $this->assertEquals('Primera', (string)$planta);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $planta = PlantaText::fromNullableString('Primera');
        $this->assertInstanceOf(PlantaText::class, $planta);
        $this->assertEquals('Primera', $planta->value());
    }

    public function test_fromNullableString_returns_null_for_null()
    {
        $this->assertNull(PlantaText::fromNullableString(null));
    }

    public function test_fromNullableString_returns_null_for_empty_string()
    {
        $this->assertNull(PlantaText::fromNullableString(''));
    }
}
