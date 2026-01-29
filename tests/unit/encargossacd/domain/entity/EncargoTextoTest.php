<?php

namespace Tests\unit\encargossacd\domain\entity;

use src\encargossacd\domain\entity\EncargoTexto;
use src\encargossacd\domain\value_objects\EncargoText;
use src\encargossacd\domain\value_objects\EncargoTextClave;
use src\shared\domain\value_objects\LocaleCode;
use Tests\myTest;

class EncargoTextoTest extends myTest
{
    private EncargoTexto $EncargoTexto;

    public function setUp(): void
    {
        parent::setUp();
        $this->EncargoTexto = new EncargoTexto();
        $this->EncargoTexto->setId_item(1);
        $this->EncargoTexto->setIdiomaVo(new LocaleCode('ts_TS.UTF-8'));
    }

    public function test_set_and_get_id_item()
    {
        $this->EncargoTexto->setId_item(1);
        $this->assertEquals(1, $this->EncargoTexto->getId_item());
    }

    public function test_set_and_get_idioma()
    {
        $idiomaVo = new LocaleCode('ts_TS.UTF-8');
        $this->EncargoTexto->setIdiomaVo($idiomaVo);
        $this->assertInstanceOf(LocaleCode::class, $this->EncargoTexto->getIdiomaVo());
        $this->assertEquals('ts_TS.UTF-8', $this->EncargoTexto->getIdiomaVo()->value());
    }

    public function test_set_and_get_clave()
    {
        $claveVo = new EncargoTextClave('Test');
        $this->EncargoTexto->setClaveVo($claveVo);
        $this->assertInstanceOf(EncargoTextClave::class, $this->EncargoTexto->getClaveVo());
        $this->assertEquals('Test', $this->EncargoTexto->getClaveVo()->value());
    }

    public function test_set_and_get_texto()
    {
        $textoVo = new EncargoText('Test');
        $this->EncargoTexto->setTextoVo($textoVo);
        $this->assertInstanceOf(EncargoText::class, $this->EncargoTexto->getTextoVo());
        $this->assertEquals('Test', $this->EncargoTexto->getTextoVo()->value());
    }

    public function test_set_all_attributes()
    {
        $encargoTexto = new EncargoTexto();
        $attributes = [
            'id_item' => 1,
            'idioma' => new LocaleCode('ts_TS.UTF-8'),
            'clave' => new EncargoTextClave('Test'),
            'texto' => new EncargoText('Test'),
        ];
        $encargoTexto->setAllAttributes($attributes);

        $this->assertEquals(1, $encargoTexto->getId_item());
        $this->assertEquals('ts_TS.UTF-8', $encargoTexto->getIdiomaVo()->value());
        $this->assertEquals('Test', $encargoTexto->getClaveVo()->value());
        $this->assertEquals('Test', $encargoTexto->getTextoVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $encargoTexto = new EncargoTexto();
        $attributes = [
            'id_item' => 1,
            'idioma' => 'ts_TS.UTF-8',
            'clave' => 'Test',
            'texto' => 'Test',
        ];
        $encargoTexto->setAllAttributes($attributes);

        $this->assertEquals(1, $encargoTexto->getId_item());
        $this->assertEquals('ts_TS.UTF-8', $encargoTexto->getIdiomaVo()->value());
        $this->assertEquals('Test', $encargoTexto->getClaveVo()->value());
        $this->assertEquals('Test', $encargoTexto->getTextoVo()->value());
    }
}
