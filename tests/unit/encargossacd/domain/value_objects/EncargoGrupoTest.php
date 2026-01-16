<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\EncargoGrupo;
use Tests\myTest;

class EncargoGrupoTest extends myTest
{
    public function test_create_valid_encargoGrupo()
    {
        $encargoGrupo = new EncargoGrupo(EncargoGrupo::CENTRO_SV);
        $this->assertEquals(1, $encargoGrupo->value());
    }

    public function test_equals_returns_true_for_same_encargoGrupo()
    {
        $encargoGrupo1 = new EncargoGrupo(EncargoGrupo::CENTRO_SV);
        $encargoGrupo2 = new EncargoGrupo(EncargoGrupo::CENTRO_SV);
        $this->assertTrue($encargoGrupo1->equals($encargoGrupo2));
    }

    public function test_equals_returns_false_for_different_encargoGrupo()
    {
        $encargoGrupo1 = new EncargoGrupo(EncargoGrupo::CENTRO_SV);
        $encargoGrupo2 = new EncargoGrupo(EncargoGrupo::CENTRO_SF);
        $this->assertFalse($encargoGrupo1->equals($encargoGrupo2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $encargoGrupo = EncargoGrupo::fromNullableInt(EncargoGrupo::CENTRO_SV);
        $this->assertInstanceOf(EncargoGrupo::class, $encargoGrupo);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $encargoGrupo = EncargoGrupo::fromNullableInt(null);
        $this->assertNull($encargoGrupo);
    }

}
