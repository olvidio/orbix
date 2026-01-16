<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\PersonaNotaPk;
use Tests\myTest;

class PersonaNotaPkTest extends myTest
{
    // * id_nom + id_nivel + tipo_acta

    public function test_equals_returns_true_for_same_personaNotaPk()
    {
        $personaNotaPk1 = new PersonaNotaPk(1001234, 2345, 1);
        $personaNotaPk2 = new PersonaNotaPk(1001234, 2345, 1);
        $this->assertTrue($personaNotaPk1->equals($personaNotaPk2));
    }

    public function test_equals_returns_false_for_different_personaNotaPk()
    {
        $personaNotaPk1 = new PersonaNotaPk(1001234, 2345, 1);
        $personaNotaPk2 = new PersonaNotaPk(1003234, 2645, 2);
        $this->assertFalse($personaNotaPk1->equals($personaNotaPk2));
    }

    public function test_getters_return_correct_values()
    {
        $personaNotaPk = new PersonaNotaPk(1001234, 2345, 1);
        $this->assertEquals(1001234, $personaNotaPk->idNom());
        $this->assertEquals(2345, $personaNotaPk->idNivel());
        $this->assertEquals(1, $personaNotaPk->tipoActa());
    }

    public function test_fromArray_creates_valid_personaNotaPk()
    {
        $array = ['id_nom' => 1001234, 'id_nivel' => 2345, 'tipo_acta' => 1];
        $personaNotaPk = PersonaNotaPk::fromArray($array);
        $this->assertEquals(1001234, $personaNotaPk->idNom());
        $this->assertEquals(2345, $personaNotaPk->idNivel());
        $this->assertEquals(1, $personaNotaPk->tipoActa());
    }

    public function test_create_personaNotaPk_with_negative_id_nivel()
    {
        $personaNotaPk = new PersonaNotaPk(1001234, -100, 1);
        $this->assertEquals(-100, $personaNotaPk->idNivel());
    }

}
