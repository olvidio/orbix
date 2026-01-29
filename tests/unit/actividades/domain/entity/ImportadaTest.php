<?php

namespace Tests\unit\actividades\domain\entity;

use src\actividades\domain\entity\Importada;
use Tests\myTest;

class ImportadaTest extends myTest
{
    private Importada $Importada;

    public function setUp(): void
    {
        parent::setUp();
        $this->Importada = new Importada();
        $this->Importada->setId_activ(1);
    }

    public function test_set_and_get_id_activ()
    {
        $this->Importada->setId_activ(1);
        $this->assertEquals(1, $this->Importada->getId_activ());
    }

    public function test_set_all_attributes()
    {
        $importada = new Importada();
        $attributes = [
            'id_activ' => 1,
        ];
        $importada->setAllAttributes($attributes);

        $this->assertEquals(1, $importada->getId_activ());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $importada = new Importada();
        $attributes = [
            'id_activ' => 1,
        ];
        $importada->setAllAttributes($attributes);

        $this->assertEquals(1, $importada->getId_activ());
    }
}
