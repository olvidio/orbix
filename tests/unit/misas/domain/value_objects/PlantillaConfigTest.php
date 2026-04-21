<?php

namespace Tests\unit\misas\domain\value_objects;

use ReflectionClass;
use src\misas\domain\value_objects\PlantillaConfig;
use Tests\myTest;

class PlantillaConfigTest extends myTest
{
    public function test_plantilla_type_constants(): void
    {
        $this->assertSame('s1', PlantillaConfig::PLANTILLA_SEMANAL_UNO);
        $this->assertSame('d1', PlantillaConfig::PLANTILLA_DOMINGOS_UNO);
        $this->assertSame('m1', PlantillaConfig::PLANTILLA_MENSUAL_UNO);
        $this->assertSame('s3', PlantillaConfig::PLANTILLA_SEMANAL_TRES);
        $this->assertSame('d3', PlantillaConfig::PLANTILLA_DOMINGOS_TRES);
        $this->assertSame('m3', PlantillaConfig::PLANTILLA_MENSUAL_TRES);
        $this->assertSame('p', PlantillaConfig::PLAN_DE_MISAS);
    }

    public function test_interval_constants_are_iso8601_duration(): void
    {
        $this->assertSame('P7D', PlantillaConfig::INTERVAL_SEMANAL);
        $this->assertSame('P11D', PlantillaConfig::INTERVAL_DOMINGOS);
        $this->assertSame('P35D', PlantillaConfig::INTERVAL_MENSUAL);
    }

    public function test_cannot_instantiate(): void
    {
        $ref = new ReflectionClass(PlantillaConfig::class);
        $this->assertFalse($ref->isInstantiable());
    }
}
