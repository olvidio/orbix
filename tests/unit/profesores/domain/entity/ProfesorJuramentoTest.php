<?php

namespace Tests\unit\profesores\domain\entity;

use src\profesores\domain\entity\ProfesorJuramento;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class ProfesorJuramentoTest extends myTest
{
    private ProfesorJuramento $ProfesorJuramento;

    public function setUp(): void
    {
        parent::setUp();
        $this->ProfesorJuramento = new ProfesorJuramento();
        $this->ProfesorJuramento->setId_item(1);
        $this->ProfesorJuramento->setId_nom(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->ProfesorJuramento->setId_item(1);
        $this->assertEquals(1, $this->ProfesorJuramento->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->ProfesorJuramento->setId_nom(1);
        $this->assertEquals(1, $this->ProfesorJuramento->getId_nom());
    }

    public function test_set_and_get_f_juramento()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ProfesorJuramento->setF_juramento($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ProfesorJuramento->getF_juramento());
        $this->assertEquals('2024-01-15 10:30:00', $this->ProfesorJuramento->getF_juramento()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes()
    {
        $profesorJuramento = new ProfesorJuramento();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'f_juramento' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $profesorJuramento->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorJuramento->getId_item());
        $this->assertEquals(1, $profesorJuramento->getId_nom());
        $this->assertEquals('2024-01-15 10:30:00', $profesorJuramento->getF_juramento()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes_with_string_values()
    {
        $profesorJuramento = new ProfesorJuramento();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'f_juramento' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $profesorJuramento->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorJuramento->getId_item());
        $this->assertEquals(1, $profesorJuramento->getId_nom());
        $this->assertEquals('2024-01-15 10:30:00', $profesorJuramento->getF_juramento()->format('Y-m-d H:i:s'));
    }
}
