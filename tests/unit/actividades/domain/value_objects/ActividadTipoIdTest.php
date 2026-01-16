<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\ActividadTipoId;
use Tests\myTest;

class ActividadTipoIdTest extends myTest
{
    // OJO debe tener exactamente 6 dígitos
    public function test_create_valid_actividadTipoId()
    {
        $actividadTipoId = new ActividadTipoId(123456);
        $this->assertEquals(123456, $actividadTipoId->value());
    }

    public function test_equals_returns_true_for_same_actividadTipoId()
    {
        $actividadTipoId1 = new ActividadTipoId(123456);
        $actividadTipoId2 = new ActividadTipoId(123456);
        $this->assertTrue($actividadTipoId1->equals($actividadTipoId2));
    }

    public function test_equals_returns_false_for_different_actividadTipoId()
    {
        $actividadTipoId1 = new ActividadTipoId(123456);
        $actividadTipoId2 = new ActividadTipoId(456456);
        $this->assertFalse($actividadTipoId1->equals($actividadTipoId2));
    }

}
