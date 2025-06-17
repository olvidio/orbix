<?php

namespace Tests\unit\usuarios\domain\entity;

use src\usuarios\domain\entity\Preferencia;
use src\usuarios\domain\value_objects\TipoPreferencia;
use src\usuarios\domain\value_objects\ValorPreferencia;
use Tests\myTest;

class PreferenciaTest extends myTest
{
    private Preferencia $preferencia;

    public function setUp(): void
    {
        parent::setUp();
        $this->preferencia = new Preferencia();
        $this->preferencia->setTipo(new TipoPreferencia('theme'));
        $this->preferencia->setId_usuario(1);
    }

    public function test_get_tipo()
    {
        $this->assertInstanceOf(TipoPreferencia::class, $this->preferencia->getTipo());
        $this->assertEquals('theme', $this->preferencia->getTipoAsString());
    }

    public function test_set_and_get_tipo()
    {
        $tipoPreferencia = new TipoPreferencia('language');
        $this->preferencia->setTipo($tipoPreferencia);
        $this->assertInstanceOf(TipoPreferencia::class, $this->preferencia->getTipo());
        $this->assertEquals('language', $this->preferencia->getTipoAsString());
    }

    public function test_get_preferencia()
    {
        $this->assertNull($this->preferencia->getPreferencia());
        $this->assertNull($this->preferencia->getPreferenciaAsString());
    }

    public function test_set_and_get_preferencia()
    {
        $valorPreferencia = new ValorPreferencia('dark');
        $this->preferencia->setPreferencia($valorPreferencia);
        $this->assertInstanceOf(ValorPreferencia::class, $this->preferencia->getPreferencia());
        $this->assertEquals('dark', $this->preferencia->getPreferenciaAsString());
    }

    public function test_get_id_usuario()
    {
        $this->assertEquals(1, $this->preferencia->getId_usuario());
    }

    public function test_set_and_get_id_usuario()
    {
        $this->preferencia->setId_usuario(2);
        $this->assertEquals(2, $this->preferencia->getId_usuario());
    }

    public function test_set_all_attributes()
    {
        $preferencia = new Preferencia();
        $attributes = [
            'tipo' => new TipoPreferencia('theme'),
            'preferencia' => new ValorPreferencia('dark'),
            'id_usuario' => 1
        ];
        $preferencia->setAllAttributes($attributes);

        $this->assertEquals('theme', $preferencia->getTipoAsString());
        $this->assertEquals('dark', $preferencia->getPreferenciaAsString());
        $this->assertEquals(1, $preferencia->getId_usuario());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $preferencia = new Preferencia();
        $attributes = [
            'tipo' => 'theme',
            'preferencia' => 'dark',
            'id_usuario' => 1
        ];
        $preferencia->setAllAttributes($attributes);

        $this->assertEquals('theme', $preferencia->getTipoAsString());
        $this->assertEquals('dark', $preferencia->getPreferenciaAsString());
        $this->assertEquals(1, $preferencia->getId_usuario());
    }
}