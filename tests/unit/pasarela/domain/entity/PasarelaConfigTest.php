<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\domain\entity;

use src\pasarela\domain\entity\PasarelaConfig;
use src\pasarela\domain\value_objects\PasarelaParametroCode;
use Tests\myTest;

final class PasarelaConfigTest extends myTest
{
    private PasarelaConfig $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->config = new PasarelaConfig();
    }

    public function test_nom_parametro_por_defecto_es_cadena_vacia(): void
    {
        $this->assertSame('', $this->config->getNom_parametro());
    }

    public function test_set_and_get_nom_parametro(): void
    {
        $this->config->setNom_parametro('curso_crt');
        $this->assertSame('curso_crt', $this->config->getNom_parametro());
        $this->assertSame('curso_crt', $this->config->getNomParametroVo()->value());
    }

    public function test_set_nom_parametro_vacio_lo_deja_sin_valor(): void
    {
        $this->config->setNom_parametro('curso_crt');
        $this->config->setNom_parametro('  ');
        $this->assertSame('', $this->config->getNom_parametro());
    }

    public function test_get_nom_parametro_vo_sin_valor_lanza_excepcion(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->config->getNomParametroVo();
    }

    public function test_set_nom_parametro_invalido_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->config->setNom_parametro('con espacio');
    }

    public function test_set_nom_parametro_vo_acepta_vo_y_string(): void
    {
        $this->config->setNomParametroVo(new PasarelaParametroCode('a'));
        $this->assertSame('a', $this->config->getNom_parametro());

        $this->config->setNomParametroVo('b');
        $this->assertSame('b', $this->config->getNom_parametro());
    }

    public function test_json_valor_por_defecto(): void
    {
        // Sin valor: null como objeto y [] en modo array (contrato de ConverterJson).
        $this->assertNull($this->config->getJson_valor());
        $this->assertSame([], $this->config->getJson_valor(true));
    }

    public function test_json_valor_ida_y_vuelta_con_array(): void
    {
        $this->config->setJson_valor(['idioma' => 'es', 'activo' => true, 'n' => 3]);
        $this->assertSame(['idioma' => 'es', 'activo' => true, 'n' => 3], $this->config->getJson_valor(true));
    }

    public function test_json_valor_acepta_string_json(): void
    {
        $this->config->setJson_valor('{"a":1}');
        $this->assertSame(['a' => 1], $this->config->getJson_valor(true));
    }

    public function test_json_valor_array_vacio_se_guarda_como_null(): void
    {
        $this->config->setJson_valor(['a' => 1]);
        $this->config->setJson_valor([]);
        $this->assertNull($this->config->getJson_valor());
        $this->assertSame([], $this->config->getJson_valor(true));
    }

    public function test_json_valor_null_borra_el_valor(): void
    {
        $this->config->setJson_valor(['a' => 1]);
        $this->config->setJson_valor(null);
        $this->assertNull($this->config->getJson_valor());
        $this->assertSame([], $this->config->getJson_valor(true));
    }

    public function test_json_valor_conserva_unicode(): void
    {
        $this->config->setJson_valor(['txt' => 'añadir ñ']);
        $this->assertSame(['txt' => 'añadir ñ'], $this->config->getJson_valor(true));
    }
}
