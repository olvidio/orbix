<?php

namespace Tests\unit\actividadestudios\domain\value_objects;

use src\actividadestudios\domain\value_objects\AvisProfesor;
use Tests\myTest;

class AvisProfesorTest extends myTest
{
    public function test_create_valid_avisProfesor()
    {
        $avisProfesor = new AvisProfesor('test value');
        $this->assertEquals('test value', $avisProfesor->value());
    }

    public function test_to_string_returns_avisProfesor_value()
    {
        $avisProfesor = new AvisProfesor('test value');
        $this->assertEquals('test value', (string)$avisProfesor);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $avisProfesor = AvisProfesor::fromNullableString('test value');
        $this->assertInstanceOf(AvisProfesor::class, $avisProfesor);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $avisProfesor = AvisProfesor::fromNullableString(null);
        $this->assertNull($avisProfesor);
    }

}
