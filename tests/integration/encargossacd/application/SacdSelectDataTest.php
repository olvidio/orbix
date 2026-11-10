<?php

namespace Tests\integration\encargossacd\application;

use src\encargossacd\application\SacdSelectData;
use Tests\myTest;

class SacdSelectDataTest extends myTest
{
    private function opciones(string $filtro, int $idNom = 0): array
    {
        return $GLOBALS['container']->get(SacdSelectData::class)->execute($filtro, $idNom);
    }

    public function test_sin_filtro_lista_los_sacd_activos(): void
    {
        $out = $this->opciones('');

        $this->assertSame(['opciones', 'selected', 'label_prefix'], array_keys($out));
        $this->assertNotEmpty($out['opciones']);
    }

    public function test_un_filtro_normal_reduce_o_iguala_la_lista(): void
    {
        $todos = $this->opciones('');
        $filtrado = $this->opciones('n');

        $this->assertLessThanOrEqual(count($todos['opciones']), count($filtrado['opciones']));
    }

    /**
     * Los valores que ofrece el desplegable del frontend (SacdFichaAjaxHashes::opcionesFiltroSacd).
     *
     * @dataProvider filtrosLegitimosProvider
     */
    public function test_los_filtros_del_frontend_son_validos(string $filtro): void
    {
        $out = $this->opciones($filtro);

        $this->assertIsArray($out['opciones']);
        $this->assertArrayHasKey("selected", $out);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function filtrosLegitimosProvider(): iterable
    {
        foreach (['n', 'a', 'sssc', 'cp_sss'] as $f) {
            yield $f => [$f];
        }
    }

    public function test_el_id_seleccionado_se_devuelve_tal_cual(): void
    {
        $this->assertSame(1234, $this->opciones('n', 1234)['selected']);
        $this->assertSame(1234, $this->opciones('', 1234)['selected']);
    }

    /**
     * Inyección SQL: addslashes() no protege en PostgreSQL con standard_conforming_strings=on
     * (la barra invertida no escapa), así que «' OR 1=1 --» devolvía a todas las personas.
     *
     * @dataProvider filtrosMaliciososProvider
     */
    public function test_un_filtro_malicioso_no_devuelve_nada_ni_amplia_el_resultado(string $filtro): void
    {
        $out = $this->opciones($filtro, 5);

        $this->assertSame([], $out['opciones']);
        $this->assertSame(5, $out['selected']);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function filtrosMaliciososProvider(): iterable
    {
        yield 'OR 1=1' => ["' OR 1=1 --"];
        yield 'UNION con otra tabla' => ["' UNION SELECT id_usuario, usuario FROM aux_usuarios --"];
        yield 'comilla simple' => ["n'"];
        yield 'con espacios' => ['n a'];
        yield 'mayúsculas' => ['N'];
        yield 'punto y coma' => ['n;'];
        yield 'demasiado largo' => [str_repeat('a', 11)];
        yield 'barra invertida' => ['n\\'];
        yield 'salto de línea interior' => ["n\nx"];
    }

    public function test_un_filtro_con_espacios_alrededor_se_recorta(): void
    {
        $this->assertEquals($this->opciones('n'), $this->opciones('  n  '));
    }
}
