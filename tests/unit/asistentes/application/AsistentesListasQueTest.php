<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\PosiblesCa;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\asistentes\application\AsistenteMoverData;
use src\asistentes\application\ListaUltimQueCtrData;
use src\asistentes\application\QueCtrListaData;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\entity\Asistente;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;

/** Selector de centro, formulario de lista por centro y modal de «mover asistente». */
final class AsistentesListasQueTest extends myTest
{
    // ---- ListaUltimQueCtrData ----------------------------------------------------------------------

    public function test_los_centros_salen_en_orden_natural_con_todos_al_final_y_se_saltan_los_inexistentes(): void
    {
        $personas = $this->createMock(PersonaSRepositoryInterface::class);
        $personas->method('getArrayIdCentros')->willReturn([3, 1, 2, 4]);
        $centros = $this->createMock(CentroDlRepositoryInterface::class);
        $nombres = [1 => 'Centro 10', 2 => 'centro 9', 3 => 'Zeta'];
        $centros->method('findById')->willReturnCallback(function (int $id) use ($nombres): ?CentroDl {
            if (!isset($nombres[$id])) {
                return null;
            }
            $c = $this->createMock(CentroDl::class);
            $c->method('getNombre_ubi')->willReturn($nombres[$id]);

            return $c;
        });

        $r = (new ListaUltimQueCtrData($personas, $centros))->build(['que' => 'pendientes', 'curso' => '2025']);

        $this->assertSame([2, 1, 3, 999], array_keys($r['opciones_centros']));
        $this->assertSame('id_ubi', $r['hash_main']['campos_form']);
        $this->assertSame(['que' => 'pendientes', 'curso' => '2025'], $r['hash_main']['campos_hidden']);
        $this->assertSame('frontend/asistentes/controller/lista_ultima_activ.php', $r['paths']['form_action']);
    }

    // ---- QueCtrListaData ----------------------------------------------------------------------

    private function que(?CentroDlRepositoryInterface $centros = null): QueCtrListaData
    {
        $centros ??= $this->createMock(CentroDlRepositoryInterface::class);

        return new QueCtrListaData($centros);
    }

    /**
     * @dataProvider listasProvider
     */
    public function test_cada_lista_apunta_a_su_pantalla_con_sus_campos_ocultos(string $lista, string $action, array $ocultos, bool $conPeriodo): void
    {
        $r = $this->que()->build(['lista' => $lista, 'tipo' => 'n', 'ssfsv' => 'sv', 'sasistentes' => 'n', 'sactividad' => 'ca']);

        $this->assertSame($action, $r['action']);
        $this->assertSame($ocultos, $r['hash_main']['campos_hidden']);
        $this->assertSame($conPeriodo, $r['periodo_form'] !== null);
    }

    /**
     * @return iterable<string, array{string, string, array<string, string>, bool}>
     */
    public static function listasProvider(): iterable
    {
        $comunes = ['tipo' => 'n', 'ssfsv' => 'sv', 'sasistentes' => 'n', 'sactividad' => 'ca'];
        yield 'actividades' => ['list_activ', 'frontend/asistentes/controller/lista_activ_ctr.php', $comunes, true];
        yield 'centros de fuera' => ['ctrex', 'frontend/asistentes/controller/lista_activ_ctr.php', $comunes, false];
        yield 'estudios' => ['list_est', 'frontend/asistentes/controller/lista_est_ctr.php', $comunes, true];
        yield 'profesión' => ['profesion', 'programas/sm-agd/lista_profesion.php', ['tipo' => 'n'], false];
        yield 'desconocida' => ['otra', '', [], false];
    }

    /**
     * @dataProvider gruposProvider
     */
    public function test_solo_se_marca_el_grupo_pedido(string $nAgd, string $marcado): void
    {
        $r = $this->que()->build(['n_agd' => $nAgd]);

        $marcas = array_filter(['n' => $r['n'], 'nj' => $r['nj'], 'nm' => $r['nm'], 'a' => $r['a'], 'sssc' => $r['sssc'], 'nax' => $r['nax'], 'c' => $r['c']]);
        $this->assertSame($marcado === '' ? [] : [$marcado => 'checked'], $marcas);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function gruposProvider(): iterable
    {
        yield 'n' => ['n', 'n'];
        yield 'nj' => ['nj', 'nj'];
        yield 'nm' => ['nm', 'nm'];
        yield 'a' => ['a', 'a'];
        yield 'agd es a' => ['agd', 'a'];
        yield 'sssc' => ['sssc', 'sssc'];
        yield 'nax' => ['nax', 'nax'];
        yield 'c' => ['c', 'c'];
        yield 'ninguno' => ['', ''];
        yield 'desconocido' => ['zz', ''];
    }

    public function test_los_centros_ofrecidos_son_los_activos_de_n_y_agd(): void
    {
        $centros = $this->createMock(CentroDlRepositoryInterface::class);
        $centros->expects($this->once())->method('getArrayCentros')->with("WHERE active = 't' AND tipo_ctr ~ '^a|^n' ")->willReturn([1 => 'Centro A']);

        $r = $this->que($centros)->build(['id_ubi' => '1']);

        $this->assertSame([1 => 'Centro A'], $r['opciones_centros']);
        $this->assertSame(1, $r['id_ubi_sel']);
    }

    /**
     * @dataProvider periodosProvider
     */
    public function test_el_periodo_por_defecto_sigue_al_tipo_de_actividad_salvo_que_se_pida_otro(string $actividad, string $periodo, string $esperado): void
    {
        $r = $this->que()->build(['lista' => 'list_activ', 'sactividad' => $actividad, 'periodo' => $periodo, 'year' => '2024']);

        $this->assertSame($esperado, $r['periodo_form']['periodo_sel']);
        $this->assertSame(2024, $r['periodo_form']['year_sel']);
        $this->assertSame(['curso_ca', 'curso_crt', 'tot_any', 'separador', 'otro'], array_keys($r['periodo_form']['opciones_periodos']));
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function periodosProvider(): iterable
    {
        yield 'ca' => ['ca', '', 'curso_ca'];
        yield 'crt' => ['crt', '', 'curso_crt'];
        yield 'otra actividad' => ['cv', '', 'tot_any'];
        yield 'el periodo pedido manda' => ['ca', 'otro', 'otro'];
    }

    public function test_sin_anyo_se_usa_el_actual(): void
    {
        $r = $this->que()->build(['lista' => 'list_est']);

        $this->assertSame((int) date('Y'), $r['periodo_form']['year_sel']);
        $this->assertSame('n_agd!empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val', $r['hash_main']['campos_form']);
    }

    // ---- AsistenteMoverData ----------------------------------------------------------------------

    private AsistenteActividadService&MockObject $serv;
    private AsistenteApplicationService&MockObject $app;

    private function mover(): AsistenteMoverData
    {
        $this->serv = $this->createMock(AsistenteActividadService::class);
        $this->app = $this->createMock(AsistenteApplicationService::class);

        return new AsistenteMoverData(
            $this->serv, $this->app,
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(ActividadRepositoryInterface::class),
            $this->createMock(ResumenPlazasService::class),
            $this->createMock(PlazaPeticionRepositoryInterface::class),
            $this->createMock(ActividadAsignaturaDlRepositoryInterface::class),
            $this->createMock(PosiblesCa::class),
        );
    }

    private function assertAviso(array $r): void
    {
        $this->assertNotSame('', $r['aviso_txt']);
        $this->assertSame('src/asistentes/asistente_guardar', $r['paths']['guardar']);
        $this->assertArrayNotHasKey('opciones_actividades', $r);
    }

    public function test_si_la_persona_o_la_actividad_no_son_validas_se_devuelve_el_aviso_de_la_excepcion(): void
    {
        $uc = $this->mover();
        $this->serv->method('getRepoAsistente')->willThrowException(new \RuntimeException('dl ajena'));

        $r = $uc->build(['id_nom' => '10011', 'id_activ' => '3001']);

        $this->assertSame('dl ajena', $r['aviso_txt']);
        $this->assertSame('src/asistentes/asistente_guardar', $r['paths']['guardar']);
    }

    public function test_si_no_existe_el_asistente_avisa_con_los_ids(): void
    {
        $uc = $this->mover();
        $this->serv->method('getRepoAsistente')->willReturn('dl');
        $this->app->method('findById')->willReturn(null);

        $r = $uc->build(['id_nom' => '10011', 'id_activ' => '3001']);

        $this->assertAviso($r);
        $this->assertStringContainsString('10011', $r['aviso_txt']);
        $this->assertStringContainsString('3001', $r['aviso_txt']);
    }

    public function test_si_los_datos_no_son_de_mi_dl_no_se_puede_mover(): void
    {
        $uc = $this->mover();
        $this->serv->method('getRepoAsistente')->willReturn('dl');
        $a = $this->createMock(Asistente::class);
        $a->method('perm_modificar')->willReturn(false);
        $this->app->method('findById')->willReturn($a);

        $this->assertAviso($uc->build(['id_nom' => '10011', 'id_activ' => '3001']));
    }

    public function test_la_seleccion_de_la_lista_da_el_id_de_persona_salvo_que_venga_id_pau(): void
    {
        $uc = $this->mover();
        $this->serv->method('getRepoAsistente')->willReturnCallback(function (int $nom, int $activ): string {
            throw new \RuntimeException("nom=$nom activ=$activ");
        });

        $this->assertSame('nom=10011 activ=3001', $uc->build(['sel' => ['10011#x'], 'id_activ' => '3001'])['aviso_txt']);
        $this->assertSame('nom=777 activ=3001', $uc->build(['sel' => ['10011#x'], 'id_pau' => '777', 'id_activ' => '3001'])['aviso_txt']);
    }
}
