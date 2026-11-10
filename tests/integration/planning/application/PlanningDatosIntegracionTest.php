<?php

namespace Tests\integration\planning\application;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\criteria\PersonaField;
use src\planning\application\PlanningCasaVerData;
use src\planning\application\PlanningCtrSelectData;
use src\planning\application\PlanningPersonaVerData;
use src\planning\application\PlanningZonesSelectData;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use Tests\myTest;

/**
 * Los casos de uso de planning no tienen persistencia propia: se ejecutan con los repositorios y
 * servicios reales sobre la BD de pruebas y se comprueba la forma del resultado.
 */
class PlanningDatosIntegracionTest extends myTest
{
    private const INI_ISO = '2025-04-01';
    private const FIN_ISO = '2025-06-30';

    public function test_ctr_select_con_un_centro_inexistente_avisa(): void
    {
        $out = $GLOBALS['container']->get(PlanningCtrSelectData::class)->execute(
            ['ctr' => 'zzzz_centro_que_no_existe_9'],
            new DateTimeLocal(self::INI_ISO),
            (new DateTimeLocal(self::INI_ISO))->getFromLocal(),
            self::FIN_ISO,
            self::INI_ISO
        );

        $this->assertSame('No encuentro este ctr', $out['msg_txt']);
        $this->assertSame('', $out['cabecera_title']);
        $this->assertSame([], $out['a_actividades2']);
    }

    public function test_ctr_select_con_un_centro_real_devuelve_la_estructura(): void
    {
        $centros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class)->matching(Criteria::create()->limit(1));
        if ($centros === []) {
            $this->markTestSkipped('No hay centros en la BD de pruebas.');
        }
        $nombre = $centros[0]->getNombre_ubi();

        $out = $GLOBALS['container']->get(PlanningCtrSelectData::class)->execute(
            ['ctr' => $nombre],
            new DateTimeLocal(self::INI_ISO),
            (new DateTimeLocal(self::INI_ISO))->getFromLocal(),
            self::FIN_ISO,
            self::INI_ISO
        );

        $this->assertSame(['msg_txt', 'cabecera_title', 'a_actividades2'], array_keys($out));
        $this->assertIsString($out['msg_txt']);
        $this->assertIsArray($out['a_actividades2']);
        // O hay personas (cabecera con el nombre) o se avisa de que no las hay: nunca las dos cosas vacías.
        $this->assertTrue($out['cabecera_title'] !== '' || $out['msg_txt'] !== '');
    }

    public function test_persona_ver_con_ids_reales_devuelve_una_entrada_por_persona(): void
    {
        $personas = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class)->matching(
            Criteria::create()->where(Filter::eq(PersonaField::Situacion, 'A'))->limit(2)
        );
        if (count($personas) < 1) {
            $this->markTestSkipped('No hay personas activas en la BD de pruebas.');
        }
        $ids = array_map(static fn ($p) => (string) $p->getId_nom(), $personas);

        $out = $GLOBALS['container']->get(PlanningPersonaVerData::class)->execute(
            [],
            $ids,
            new DateTimeLocal(self::INI_ISO),
            (new DateTimeLocal(self::INI_ISO))->getFromLocal(),
            self::FIN_ISO,
            self::INI_ISO
        );

        $this->assertSame(['a_actividades'], array_keys($out));
        $this->assertIsArray($out['a_actividades']);
    }

    public function test_persona_ver_sin_ids_devuelve_vacio(): void
    {
        $out = $GLOBALS['container']->get(PlanningPersonaVerData::class)->execute(
            [],
            [],
            new DateTimeLocal(self::INI_ISO),
            (new DateTimeLocal(self::INI_ISO))->getFromLocal(),
            self::FIN_ISO,
            self::INI_ISO
        );

        $this->assertSame(['a_actividades' => []], $out);
    }

    public function test_casa_ver_devuelve_actividades_y_periodos(): void
    {
        $out = $GLOBALS['container']->get(PlanningCasaVerData::class)->execute([
            'cdc_sel' => '1',
            'f_ini_iso' => self::INI_ISO,
            'f_fin_iso' => self::FIN_ISO,
        ]);

        $this->assertSame(['a_actividades', 'casa_periodos_por_ubi'], array_keys($out));
        $this->assertIsArray($out['a_actividades']);
        $this->assertIsArray($out['casa_periodos_por_ubi']);
    }

    public function test_casa_ver_sin_fechas_lanza_excepcion(): void
    {
        $this->expectException(\RuntimeException::class);
        $GLOBALS['container']->get(PlanningCasaVerData::class)->execute(['cdc_sel' => '1']);
    }

    public function test_zones_select_con_una_zona_real_devuelve_el_periodo_del_trimestre(): void
    {
        $zonas = $GLOBALS['container']->get(ZonaRepositoryInterface::class)->getArrayZonas();
        if ($zonas === []) {
            $this->markTestSkipped('No hay zonas en la BD de pruebas.');
        }
        $idZona = (string) array_key_first($zonas);

        $out = $GLOBALS['container']->get(PlanningZonesSelectData::class)->execute([
            'id_zona' => $idZona, 'trimestre' => '1', 'year' => '2025', 'actividad' => '', 'propuesta' => '',
        ]);

        $this->assertSame('2025-01-01', $out['planning_ini_iso']);
        $this->assertSame('2025-03-31', $out['planning_fin_iso']);
        $this->assertArrayNotHasKey('oIniPlanning', $out);
        $this->assertArrayHasKey('actividades_por_zona', $out);
        $this->assertNotFalse(json_encode($out, JSON_THROW_ON_ERROR));
    }
}
