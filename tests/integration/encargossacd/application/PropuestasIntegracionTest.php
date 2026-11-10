<?php

namespace Tests\integration\encargossacd\application;

use src\encargossacd\application\PropuestasAjaxDispatch;
use src\encargossacd\application\PropuestasAjaxGetLista;
use src\encargossacd\application\PropuestasCentrosPorFiltro;
use src\encargossacd\application\PropuestasListaEncData;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use Tests\myTest;

/**
 * Casos de uso de las propuestas de encargos con repositorios reales. Los que leen la tabla staging
 * se omiten si no existe en la BD de pruebas (la crea DBPropuestas bajo demanda).
 */
class PropuestasIntegracionTest extends myTest
{
    private function existeStaging(): bool
    {
        return $GLOBALS['container']->get(PropuestaEncargoSacdRepositoryInterface::class)->existenLasTablas();
    }

    private function exigirStaging(): void
    {
        if (!$this->existeStaging()) {
            $this->markTestSkipped('No existe propuesta_encargos_sacd en la base de pruebas.');
        }
    }

    // ---- centros por filtro ------------------------------------------------------------------

    /**
     * @dataProvider filtrosProvider
     */
    public function test_centros_por_filtro_devuelve_centros_activos(int $filtro): void
    {
        $centros = $GLOBALS['container']->get(PropuestasCentrosPorFiltro::class)->execute($filtro);

        $this->assertIsArray($centros);
        foreach ($centros as $c) {
            $this->assertTrue($c instanceof CentroDl || $c instanceof CentroEllas);
        }
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function filtrosProvider(): iterable
    {
        foreach ([1 => 'numerarios, agd, sacd y of', 2 => 'ellas', 3 => 'sss', 4 => 'iglesias', 5 => 'cgi/oc'] as $f => $d) {
            yield $d => [$f];
        }
    }

    public function test_un_filtro_desconocido_no_devuelve_nada_salvo_que_se_pida_todo(): void
    {
        $uc = $GLOBALS['container']->get(PropuestasCentrosPorFiltro::class);

        $this->assertSame([], $uc->execute(0));
        $this->assertSame([], $uc->execute(99));
        $this->assertNotEmpty($uc->execute(0, todosEnDefault: true), 'con todosEnDefault devuelve todos los activos');
    }

    public function test_el_filtro_de_ellas_solo_devuelve_centros_de_ellas(): void
    {
        foreach ($GLOBALS['container']->get(PropuestasCentrosPorFiltro::class)->execute(2) as $c) {
            $this->assertInstanceOf(CentroEllas::class, $c);
        }
    }

    // ---- lista de propuestas (staging) ----------------------------------------------------------------

    public function test_get_lista_devuelve_html_por_centro(): void
    {
        $this->exigirStaging();

        $out = $GLOBALS['container']->get(PropuestasAjaxGetLista::class)->execute(3);

        $this->assertTrue($out['success']);
        $this->assertIsString($out['lista']);
    }

    public function test_lista_enc_data_con_todos_por_defecto(): void
    {
        $this->exigirStaging();

        $out = $GLOBALS['container']->get(PropuestasListaEncData::class)->execute(0);

        $this->assertSame(['html'], array_keys($out));
        $this->assertIsString($out['html']);
        $this->assertNotSame('', $out['html'], 'con todos los centros activos hay HTML');
    }

    public function test_lista_enc_data_con_un_filtro_no_incluye_a_todos(): void
    {
        $this->exigirStaging();
        $uc = $GLOBALS['container']->get(PropuestasListaEncData::class);

        $this->assertLessThanOrEqual(strlen($uc->execute(0)['html']), strlen($uc->execute(3)['html']));
    }

    // ---- despachador ---------------------------------------------------------------------------------------

    public function test_el_despachador_rechaza_una_operacion_desconocida(): void
    {
        $out = $GLOBALS['container']->get(PropuestasAjaxDispatch::class)->execute('borrar_todo');

        $this->assertFalse($out['success']);
        $this->assertNotSame('', $out['mensaje']);
    }

    public function test_el_despachador_sin_operacion_tambien_la_rechaza(): void
    {
        $out = $GLOBALS['container']->get(PropuestasAjaxDispatch::class)->execute('');

        $this->assertFalse($out['success']);
    }

    public function test_el_despachador_get_lista_devuelve_la_lista(): void
    {
        $this->exigirStaging();

        $out = $GLOBALS['container']->get(PropuestasAjaxDispatch::class)->execute('get_lista');

        $this->assertArrayHasKey('success', $out);
    }

    // ---- lista de sacd y HTML por centro (staging) ----------------------------------------------------------

    /**
     * @dataProvider seleccionesDeSacdProvider
     */
    public function test_lista_de_propuestas_por_sacd(string $sel): void
    {
        $this->exigirStaging();

        $r = $GLOBALS['container']->get(\src\encargossacd\application\PropuestasListaSacdData::class)->execute($sel);

        $this->assertSame(['array_modo', 'lugar_fecha'], array_keys($r));
        $this->assertIsArray($r['array_modo']);
        $this->assertNotFalse(json_encode($r, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function seleccionesDeSacdProvider(): iterable
    {
        yield 'numerarios y agregados' => ['nagd'];
        yield 'sssc' => ['sssc'];
        yield 'vacía' => [''];
    }

    public function test_una_seleccion_desconocida_no_lista_sacd(): void
    {
        $this->exigirStaging();

        $r = $GLOBALS['container']->get(\src\encargossacd\application\PropuestasListaSacdData::class)->execute('otra');

        $this->assertSame([], $r['array_modo']);
    }

    public function test_html_editable_y_simple_de_un_centro_con_propuestas(): void
    {
        $this->exigirStaging();
        $html = $GLOBALS['container']->get(\src\encargossacd\application\services\PropuestasEncargosUbiHtml::class);
        $centros = $GLOBALS['container']->get(PropuestasCentrosPorFiltro::class)->execute(0, todosEnDefault: true);
        $vistos = 0;
        foreach (array_slice($centros, 0, 25) as $c) {
            $editable = $html->editable($c->getId_ubi());
            $simple = $html->simple($c->getId_ubi());
            $this->assertIsString($editable);
            $this->assertIsString($simple);
            $vistos++;
        }
        $this->assertGreaterThan(0, $vistos);
    }

    public function test_html_de_un_centro_inexistente_no_falla(): void
    {
        $this->exigirStaging();
        $html = $GLOBALS['container']->get(\src\encargossacd\application\services\PropuestasEncargosUbiHtml::class);

        $this->assertSame('', trim(strip_tags($html->simple(2147000000))));
        $this->assertIsString($html->editable(2147000000));
    }
}

