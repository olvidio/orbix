<?php

namespace Tests\unit\actividadessacd\domain\entity;

use src\actividadessacd\domain\entity\ActividadSacdTexto;
use src\actividadessacd\domain\value_objects\SacdTextoClave;
use src\actividadessacd\domain\value_objects\SacdTextoTexto;
use src\encargossacd\domain\value_objects\LocaleCode;
use Tests\myTest;

class ActividadSacdTextoTest extends myTest
{
    private ActividadSacdTexto $ActividadSacdTexto;

    public function setUp(): void
    {
        parent::setUp();
        $this->ActividadSacdTexto = new ActividadSacdTexto();
        $this->ActividadSacdTexto->setId_item(1);
        $this->ActividadSacdTexto->setIdiomaVo(new LocaleCode('TST'));
    }

    public function test_set_and_get_id_item()
    {
        $this->ActividadSacdTexto->setId_item(1);
        $this->assertEquals(1, $this->ActividadSacdTexto->getId_item());
    }

    public function test_set_and_get_idioma()
    {
        $idiomaVo = new LocaleCode('TST');
        $this->ActividadSacdTexto->setIdiomaVo($idiomaVo);
        $this->assertInstanceOf(LocaleCode::class, $this->ActividadSacdTexto->getIdiomaVo());
        $this->assertEquals('TST', $this->ActividadSacdTexto->getIdiomaVo()->value());
    }

    public function test_set_and_get_clave()
    {
        $claveVo = new SacdTextoClave('Test');
        $this->ActividadSacdTexto->setClaveVo($claveVo);
        $this->assertInstanceOf(SacdTextoClave::class, $this->ActividadSacdTexto->getClaveVo());
        $this->assertEquals('Test', $this->ActividadSacdTexto->getClaveVo()->value());
    }

    public function test_set_and_get_texto()
    {
        $textoVo = new SacdTextoTexto('Test');
        $this->ActividadSacdTexto->setTextoVo($textoVo);
        $this->assertInstanceOf(SacdTextoTexto::class, $this->ActividadSacdTexto->getTextoVo());
        $this->assertEquals('Test', $this->ActividadSacdTexto->getTextoVo()->value());
    }

    public function test_set_all_attributes()
    {
        $actividadSacdTexto = new ActividadSacdTexto();
        $attributes = [
            'id_item' => 1,
            'idioma' => new LocaleCode('TST'),
            'clave' => new SacdTextoClave('Test'),
            'texto' => new SacdTextoTexto('Test'),
        ];
        $actividadSacdTexto->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadSacdTexto->getId_item());
        $this->assertEquals('TST', $actividadSacdTexto->getIdiomaVo()->value());
        $this->assertEquals('Test', $actividadSacdTexto->getClaveVo()->value());
        $this->assertEquals('Test', $actividadSacdTexto->getTextoVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $actividadSacdTexto = new ActividadSacdTexto();
        $attributes = [
            'id_item' => 1,
            'idioma' => 'TST',
            'clave' => 'Test',
            'texto' => 'Test',
        ];
        $actividadSacdTexto->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadSacdTexto->getId_item());
        $this->assertEquals('TST', $actividadSacdTexto->getIdiomaVo()->value());
        $this->assertEquals('Test', $actividadSacdTexto->getClaveVo()->value());
        $this->assertEquals('Test', $actividadSacdTexto->getTextoVo()->value());
    }
}
