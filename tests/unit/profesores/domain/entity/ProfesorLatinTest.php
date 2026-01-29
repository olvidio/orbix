<?php

namespace Tests\unit\profesores\domain\entity;

use src\profesores\domain\entity\ProfesorLatin;
use Tests\myTest;

class ProfesorLatinTest extends myTest
{
    private ProfesorLatin $ProfesorLatin;

    public function setUp(): void
    {
        parent::setUp();
        $this->ProfesorLatin = new ProfesorLatin();
        $this->ProfesorLatin->setId_nom(1);
    }

    public function test_set_and_get_id_nom()
    {
        $this->ProfesorLatin->setId_nom(1);
        $this->assertEquals(1, $this->ProfesorLatin->getId_nom());
    }

    public function test_set_and_get_latin()
    {
        $this->ProfesorLatin->setLatin(true);
        $this->assertTrue($this->ProfesorLatin->isLatin());
    }

    public function test_set_all_attributes()
    {
        $profesorLatin = new ProfesorLatin();
        $attributes = [
            'id_nom' => 1,
            'latin' => true,
        ];
        $profesorLatin->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorLatin->getId_nom());
        $this->assertTrue($profesorLatin->isLatin());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $profesorLatin = new ProfesorLatin();
        $attributes = [
            'id_nom' => 1,
            'latin' => true,
        ];
        $profesorLatin->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorLatin->getId_nom());
        $this->assertTrue($profesorLatin->isLatin());
    }
}
