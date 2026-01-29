<?php

namespace Tests\unit\asignaturas\domain\entity;

use src\asignaturas\domain\entity\AsignaturaTipo;
use src\asignaturas\domain\value_objects\AsignaturaTipoId;
use src\asignaturas\domain\value_objects\AsignaturaTipoLatin;
use src\asignaturas\domain\value_objects\AsignaturaTipoName;
use src\asignaturas\domain\value_objects\AsignaturaTipoShortName;
use src\asignaturas\domain\value_objects\AsignaturaTipoYear;
use Tests\myTest;

class AsignaturaTipoTest extends myTest
{
    private AsignaturaTipo $AsignaturaTipo;

    public function setUp(): void
    {
        parent::setUp();
        $this->AsignaturaTipo = new AsignaturaTipo();
        $this->AsignaturaTipo->setIdTipoVo(new AsignaturaTipoId(5));
        $this->AsignaturaTipo->setTipoAsignaturaVo(new AsignaturaTipoName('Test value'));
    }

    public function test_set_and_get_id_tipo()
    {
        $id_tipoVo = new AsignaturaTipoId(6);
        $this->AsignaturaTipo->setIdTipoVo($id_tipoVo);
        $this->assertInstanceOf(AsignaturaTipoId::class, $this->AsignaturaTipo->getIdTipoVo());
        $this->assertEquals(6, $this->AsignaturaTipo->getIdTipoVo()->value());
    }

    public function test_set_and_get_tipo_asignatura()
    {
        $tipo_asignaturaVo = new AsignaturaTipoName('Test value');
        $this->AsignaturaTipo->setTipoAsignaturaVo($tipo_asignaturaVo);
        $this->assertInstanceOf(AsignaturaTipoName::class, $this->AsignaturaTipo->getTipoAsignaturaVo());
        $this->assertEquals('Test value', $this->AsignaturaTipo->getTipoAsignaturaVo()->value());
    }

    public function test_set_and_get_tipo_breve()
    {
        $tipo_breveVo = new AsignaturaTipoShortName('TT');
        $this->AsignaturaTipo->setTipoBreveVo($tipo_breveVo);
        $this->assertInstanceOf(AsignaturaTipoShortName::class, $this->AsignaturaTipo->getTipoBreveVo());
        $this->assertEquals('TT', $this->AsignaturaTipo->getTipoBreveVo()->value());
    }

    public function test_set_and_get_year()
    {
        $yearVo = new AsignaturaTipoYear('II');
        $this->AsignaturaTipo->setYearVo($yearVo);
        $this->assertInstanceOf(AsignaturaTipoYear::class, $this->AsignaturaTipo->getYearVo());
        $this->assertEquals('II', $this->AsignaturaTipo->getYearVo()->value());
    }

    public function test_set_and_get_tipo_latin()
    {
        $tipo_latinVo = new AsignaturaTipoLatin('Test value');
        $this->AsignaturaTipo->setTipoLatinVo($tipo_latinVo);
        $this->assertInstanceOf(AsignaturaTipoLatin::class, $this->AsignaturaTipo->getTipoLatinVo());
        $this->assertEquals('Test value', $this->AsignaturaTipo->getTipoLatinVo()->value());
    }

    public function test_set_all_attributes()
    {
        $asignaturaTipo = new AsignaturaTipo();
        $attributes = [
            'id_tipo' => new AsignaturaTipoId(5),
            'tipo_asignatura' => new AsignaturaTipoName('Test value'),
            'tipo_breve' => new AsignaturaTipoShortName('TT'),
            'year' => new AsignaturaTipoYear('II'),
            'tipo_latin' => new AsignaturaTipoLatin('Test value'),
        ];
        $asignaturaTipo->setAllAttributes($attributes);

        $this->assertEquals(5, $asignaturaTipo->getIdTipoVo()->value());
        $this->assertEquals('Test value', $asignaturaTipo->getTipoAsignaturaVo()->value());
        $this->assertEquals('TT', $asignaturaTipo->getTipoBreveVo()->value());
        $this->assertEquals('II', $asignaturaTipo->getYearVo()->value());
        $this->assertEquals('Test value', $asignaturaTipo->getTipoLatinVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $asignaturaTipo = new AsignaturaTipo();
        $attributes = [
            'id_tipo' => 5,
            'tipo_asignatura' => 'Test value',
            'tipo_breve' => 'TT',
            'year' => 'II',
            'tipo_latin' => 'Test value',
        ];
        $asignaturaTipo->setAllAttributes($attributes);

        $this->assertEquals(5, $asignaturaTipo->getIdTipoVo()->value());
        $this->assertEquals('Test value', $asignaturaTipo->getTipoAsignaturaVo()->value());
        $this->assertEquals('TT', $asignaturaTipo->getTipoBreveVo()->value());
        $this->assertEquals('II', $asignaturaTipo->getYearVo()->value());
        $this->assertEquals('Test value', $asignaturaTipo->getTipoLatinVo()->value());
    }
}
