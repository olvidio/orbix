<?php

namespace Tests\unit\notas\domain\entity;

use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\entity\Acta;
use src\notas\domain\value_objects\ActaNumero;
use src\notas\domain\value_objects\Libro;
use src\notas\domain\value_objects\Linea;
use src\notas\domain\value_objects\Lugar;
use src\notas\domain\value_objects\Observ;
use src\notas\domain\value_objects\Pagina;
use src\notas\domain\value_objects\Pdf;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class ActaTest extends myTest
{
    private Acta $Acta;

    public function setUp(): void
    {
        parent::setUp();
        $this->Acta = new Acta();
        $this->Acta->setActaVo(new ActaNumero('dlb 23/24'));
        $this->Acta->setF_acta(new DateTimeLocal('2024-01-15 10:30:00'));
    }

    public function test_set_and_get_acta()
    {
        $actaVo = new ActaNumero('dlb 23/24');
        $this->Acta->setActaVo($actaVo);
        $this->assertInstanceOf(ActaNumero::class, $this->Acta->getActaVo());
        $this->assertEquals('dlb 23/24', $this->Acta->getActaVo()->value());
    }

    public function test_set_and_get_id_asignatura()
    {
        $id_asignaturaVo = new AsignaturaId(1001);
        $this->Acta->setIdAsignaturaVo($id_asignaturaVo);
        $this->assertInstanceOf(AsignaturaId::class, $this->Acta->getIdAsignaturaVo());
        $this->assertEquals(1001, $this->Acta->getIdAsignaturaVo()->value());
    }

    public function test_set_and_get_id_activ()
    {
        $this->Acta->setId_activ(1);
        $this->assertEquals(1, $this->Acta->getId_activ());
    }

    public function test_set_and_get_f_acta()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->Acta->setF_acta($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->Acta->getF_acta());
        $this->assertEquals('2024-01-15 10:30:00', $this->Acta->getF_acta()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_libro()
    {
        $libroVo = new Libro(1);
        $this->Acta->setLibroVo($libroVo);
        $this->assertInstanceOf(Libro::class, $this->Acta->getLibroVo());
        $this->assertEquals(1, $this->Acta->getLibroVo()->value());
    }

    public function test_set_and_get_pagina()
    {
        $paginaVo = new Pagina(1);
        $this->Acta->setPaginaVo($paginaVo);
        $this->assertInstanceOf(Pagina::class, $this->Acta->getPaginaVo());
        $this->assertEquals(1, $this->Acta->getPaginaVo()->value());
    }

    public function test_set_and_get_linea()
    {
        $lineaVo = new Linea(1);
        $this->Acta->setLineaVo($lineaVo);
        $this->assertInstanceOf(Linea::class, $this->Acta->getLineaVo());
        $this->assertEquals(1, $this->Acta->getLineaVo()->value());
    }

    public function test_set_and_get_lugar()
    {
        $lugarVo = new Lugar('test');
        $this->Acta->setLugarVo($lugarVo);
        $this->assertInstanceOf(Lugar::class, $this->Acta->getLugarVo());
        $this->assertEquals('test', $this->Acta->getLugarVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new Observ('test');
        $this->Acta->setObservVo($observVo);
        $this->assertInstanceOf(Observ::class, $this->Acta->getObservVo());
        $this->assertEquals('test', $this->Acta->getObservVo()->value());
    }

    public function test_set_and_get_pdf()
    {
        $pdfVo = new Pdf('test');
        $this->Acta->setPdfVo($pdfVo);
        $this->assertInstanceOf(Pdf::class, $this->Acta->getPdfVo());
        $this->assertEquals('test', $this->Acta->getPdfVo()->value());
    }

    public function test_set_all_attributes()
    {
        $acta = new Acta();
        $attributes = [
            'acta' => new ActaNumero('dlb 23/24'),
            'id_asignatura' => new AsignaturaId(1001),
            'id_activ' => 1,
            'f_acta' => new DateTimeLocal('2024-01-15 10:30:00'),
            'libro' => new Libro(1),
            'pagina' => new Pagina(1),
            'linea' => new Linea(1),
            'lugar' => new Lugar('test'),
            'observ' => new Observ('test'),
            'pdf' => new Pdf('test'),
        ];
        $acta->setAllAttributes($attributes);

        $this->assertEquals('dlb 23/24', $acta->getActaVo()->value());
        $this->assertEquals(1001, $acta->getIdAsignaturaVo()->value());
        $this->assertEquals(1, $acta->getId_activ());
        $this->assertEquals('2024-01-15 10:30:00', $acta->getF_acta()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $acta->getLibroVo()->value());
        $this->assertEquals(1, $acta->getPaginaVo()->value());
        $this->assertEquals(1, $acta->getLineaVo()->value());
        $this->assertEquals('test', $acta->getLugarVo()->value());
        $this->assertEquals('test', $acta->getObservVo()->value());
        $this->assertEquals('test', $acta->getPdfVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $acta = new Acta();
        $attributes = [
            'acta' => 'dlb 23/24',
            'id_asignatura' => 1001,
            'id_activ' => 1,
            'f_acta' => new DateTimeLocal('2024-01-15 10:30:00'),
            'libro' => 1,
            'pagina' => 1,
            'linea' => 1,
            'lugar' => 'test',
            'observ' => 'test',
            'pdf' => 'test',
        ];
        $acta->setAllAttributes($attributes);

        $this->assertEquals('dlb 23/24', $acta->getActaVo()->value());
        $this->assertEquals(1001, $acta->getIdAsignaturaVo()->value());
        $this->assertEquals(1, $acta->getId_activ());
        $this->assertEquals('2024-01-15 10:30:00', $acta->getF_acta()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $acta->getLibroVo()->value());
        $this->assertEquals(1, $acta->getPaginaVo()->value());
        $this->assertEquals(1, $acta->getLineaVo()->value());
        $this->assertEquals('test', $acta->getLugarVo()->value());
        $this->assertEquals('test', $acta->getObservVo()->value());
        $this->assertEquals('test', $acta->getPdfVo()->value());
    }
}
