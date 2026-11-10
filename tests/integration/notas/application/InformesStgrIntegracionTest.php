<?php

declare(strict_types=1);

namespace Tests\integration\notas\application;

use src\notas\application\CentroEstudiosLookup;
use src\notas\application\InformeStgrAgregados;
use src\notas\application\InformeStgrNumerarios;
use src\notas\application\InformeStgrProfesores;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use Tests\myTest;

/**
 * Informes anuales STGR (numerarios, agregados, profesores) sobre la BD real, solo lectura.
 * Cubren también `Resumen` (legacy), sus tablas temporales y `ResumenFactory`, que no se pueden
 * probar con mocks. Los números dependen de los datos; se comprueban las relaciones entre ellos.
 */
final class InformesStgrIntegracionTest extends myTest
{
    private function assertCursoTxt(string $curso): void
    {
        $this->assertMatchesRegularExpression('/^\d{4}-\d{4}$/', $curso);
        [$ini, $fin] = array_map('intval', explode('-', $curso));
        $this->assertSame($ini + 1, $fin);
    }

    /**
     * @param array<int|string, mixed> $res
     * @param array<int|string, mixed> $textos
     */
    private function assertInformeCoherente(array $res, array $textos): void
    {
        $this->assertSame(array_keys($res), array_keys($textos), 'cada punto tiene su texto');
        foreach ($res as $punto => $dato) {
            $this->assertArrayHasKey('num', $dato, "punto $punto");
            $this->assertTrue(is_int($dato['num']) || is_float($dato['num']) || is_string($dato['num']), "punto $punto");
            $this->assertNotSame('', $textos[$punto], "punto $punto");
        }
    }

    // ---- numerarios ----------------------------------------------------------------------

    public function test_informe_de_numerarios_trae_los_18_puntos_y_el_total_de_bienio_suma_1_2_3(): void
    {
        $informe = $GLOBALS['container']->get(InformeStgrNumerarios::class);

        $r = $informe->calcular([], false, '');

        $this->assertSame(range(1, 18), array_keys($r['res']));
        $this->assertInformeCoherente($r['res'], $r['textos']);
        $this->assertCursoTxt($r['curso_txt']);
        $this->assertSame((int) $r['res'][1]['num'] + (int) $r['res'][2]['num'] + (int) $r['res'][3]['num'], (int) $r['res'][4]['num']);
        $this->assertSame((int) $r['res'][8]['num'] + (int) $r['res'][9]['num'], (int) $r['res'][10]['num'], 'cuadrienio = año I + años II-IV');
        $this->assertIsString($r['avisos_html']);
    }

    public function test_con_lista_se_anade_el_punto_x_de_repaso(): void
    {
        $informe = $GLOBALS['container']->get(InformeStgrNumerarios::class);

        $sin = $informe->calcular([], false, '');
        $con = $informe->calcular([], true, '');

        $this->assertArrayNotHasKey('x', $sin['res']);
        $this->assertArrayHasKey('x', $con['res']);
        foreach ($sin['res'] as $punto => $dato) {
            $this->assertSame((string) $dato['num'], (string) $con['res'][$punto]['num'], "punto $punto no cambia con la lista");
        }
    }

    public function test_los_conteos_no_cambian_entre_dos_llamadas(): void
    {
        $informe = $GLOBALS['container']->get(InformeStgrNumerarios::class);

        $a = $informe->calcular([], false, '');
        $b = $informe->calcular([], false, '');

        $this->assertSame(array_map(fn ($d) => $d['num'], $a['res']), array_map(fn ($d) => $d['num'], $b['res']));
    }

    // ---- agregados y profesores ----------------------------------------------------------------------

    public function test_informe_de_agregados(): void
    {
        $informe = $GLOBALS['container']->get(InformeStgrAgregados::class);

        $r = $informe->calcular([], false);

        $this->assertNotSame([], $r['res']);
        $this->assertInformeCoherente($r['res'], $r['textos']);
        $this->assertCursoTxt($r['curso_txt']);
    }

    public function test_informe_de_profesores(): void
    {
        $informe = $GLOBALS['container']->get(InformeStgrProfesores::class);

        $r = $informe->calcular(false);

        $this->assertNotSame([], $r['res']);
        $this->assertInformeCoherente($r['res'], $r['textos']);
        $this->assertCursoTxt($r['curso_txt']);
    }

    // ---- ce_lugar ----------------------------------------------------------------------

    public function test_el_ce_lugar_ignora_ids_no_numericos_o_inexistentes(): void
    {
        $lookup = $GLOBALS['container']->get(CentroEstudiosLookup::class);

        $this->assertSame('', $lookup->getFromDl([]));
        $this->assertSame('', $lookup->getFromDl(['abc', '', 999999999]));
    }

    public function test_el_ce_lugar_de_varias_dl_se_une_con_comas_sin_vacios(): void
    {
        $lookup = $GLOBALS['container']->get(CentroEstudiosLookup::class);
        $delegaciones = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $ids = [];
        foreach (['H', 'M'] as $region) {
            $ids = array_merge($ids, array_keys($delegaciones->getArrayDlRegionStgr([$region])));
        }
        if ($ids === []) {
            $this->markTestSkipped('No hay delegaciones de las regiones H/M en la BD.');
        }

        $todas = $lookup->getFromDl($ids);

        $this->assertDoesNotMatchRegularExpression('/(^,|,,|,$)/', $todas);
        foreach (array_slice($ids, 0, 3) as $id) {
            $una = $lookup->getFromDl([$id]);
            if ($una !== '') {
                $this->assertStringContainsString($una, $todas);
            }
        }
    }

    public function test_resolver_ce_lugar_con_dl_elegidas_usa_el_lookup(): void
    {
        $informe = $GLOBALS['container']->get(InformeStgrNumerarios::class);
        $lookup = $GLOBALS['container']->get(CentroEstudiosLookup::class);

        $this->assertSame($lookup->getFromDl(['abc']), $informe->resolverCeLugar(['abc']));
        $this->assertIsString($informe->resolverCeLugar([]));
    }
}
