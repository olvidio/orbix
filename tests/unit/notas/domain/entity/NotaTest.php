<?php

namespace Tests\unit\notas\domain\entity;

use src\notas\domain\entity\Nota;
use src\notas\domain\value_objects\Breve;
use src\notas\domain\value_objects\Descripcion;
use src\notas\domain\value_objects\NotaSituacion;
use Tests\myTest;

class NotaTest extends myTest
{
    private Nota $Nota;

    public function setUp(): void
    {
        parent::setUp();
        $this->Nota = new Nota();
        $this->Nota->setIdSituacionVo(new NotaSituacion(1));
        $this->Nota->setDescripcionVo(new Descripcion('test'));
    }

    public function test_set_and_get_id_situacion()
    {
        $id_situacionVo = new NotaSituacion(1);
        $this->Nota->setIdSituacionVo($id_situacionVo);
        $this->assertInstanceOf(NotaSituacion::class, $this->Nota->getIdSituacionVo());
        $this->assertEquals(1, $this->Nota->getIdSituacionVo()->value());
    }

    public function test_set_and_get_descripcion()
    {
        $descripcionVo = new Descripcion('test');
        $this->Nota->setDescripcionVo($descripcionVo);
        $this->assertInstanceOf(Descripcion::class, $this->Nota->getDescripcionVo());
        $this->assertEquals('test', $this->Nota->getDescripcionVo()->value());
    }

    public function test_set_and_get_superada()
    {
        $this->Nota->setSuperada(true);
        $this->assertTrue($this->Nota->isSuperada());
    }

    public function test_set_and_get_breve()
    {
        $breveVo = new Breve('test');
        $this->Nota->setBreveVo($breveVo);
        $this->assertInstanceOf(Breve::class, $this->Nota->getBreveVo());
        $this->assertEquals('test', $this->Nota->getBreveVo()->value());
    }

    public function test_set_all_attributes()
    {
        $nota = new Nota();
        $attributes = [
            'id_situacion' => new NotaSituacion(1),
            'descripcion' => new Descripcion('test'),
            'superada' => true,
            'breve' => new Breve('test'),
        ];
        $nota->setAllAttributes($attributes);

        $this->assertEquals(1, $nota->getIdSituacionVo()->value());
        $this->assertEquals('test', $nota->getDescripcionVo()->value());
        $this->assertTrue($nota->isSuperada());
        $this->assertEquals('test', $nota->getBreveVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $nota = new Nota();
        $attributes = [
            'id_situacion' => 1,
            'descripcion' => 'test',
            'superada' => true,
            'breve' => 'test',
        ];
        $nota->setAllAttributes($attributes);

        $this->assertEquals(1, $nota->getIdSituacionVo()->value());
        $this->assertEquals('test', $nota->getDescripcionVo()->value());
        $this->assertTrue($nota->isSuperada());
        $this->assertEquals('test', $nota->getBreveVo()->value());
    }
}
