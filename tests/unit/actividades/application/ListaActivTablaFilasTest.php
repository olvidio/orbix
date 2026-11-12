<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\application\ListaActivTabla;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;
use Tests\support\DescribeCriteria;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Listado de actividades (lista_activ): filtros de estado, tipo y periodo,
 * columnas según el listado y los permisos, y valores de cada fila.
 */
final class ListaActivTablaFilasTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private ActividadRepositoryInterface&MockObject $actividades;
    private CasaRepositoryInterface&MockObject $casas;
    private TipoTarifaRepositoryInterface&MockObject $tarifas;
    private CentroEncargadoRepositoryInterface&MockObject $encargados;
    private ActividadCargoRepositoryInterface&MockObject $cargos;
    /** @var list<list<string>> */
    private array $consultas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->casas = $this->createMock(CasaRepositoryInterface::class);
        $this->tarifas = $this->createMock(TipoTarifaRepositoryInterface::class);
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->encargados->method('getCentrosEncargadosActividad')->willReturn([]);
        $this->cargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $this->cargos->method('getActividadSacds')->willReturn([]);
        $tipos = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $tipos->method('getNom_tipoPosibles')->willReturn(['tipo_nom' => ['000' => '(sin especificar)', '021' => 'cuadrienio'], 'nom_tipo' => []]);
        $this->sustituirServicio(TipoDeActividadRepositoryInterface::class, $tipos);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        parent::tearDown();
    }

    private function uc(): ListaActivTabla
    {
        return new ListaActivTabla($this->actividades, $this->casas, $this->tarifas, $this->encargados, $this->cargos);
    }

    /** @param list<ActividadAll> $lista */
    private function devolver(array $lista): void
    {
        $this->actividades->method('matching')->willReturnCallback(function ($c) use ($lista): array {
            $this->consultas[] = DescribeCriteria::filtros($c);
            return $lista;
        });
    }

    private function actividad(int $id, int $tipo = 112021): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setId_tipo_activ($tipo);
        $a->setNom_activ("act $id");
        $a->setF_ini(new DateTimeLocal('2027-03-05'));
        $a->setF_fin(new DateTimeLocal('2027-03-08'));
        $a->setH_ini(TimeLocal::fromString('20:30:00'));
        $a->setH_fin(TimeLocal::fromString('10:15:00'));
        $a->setId_ubi(10200);
        $a->setTarifa(4);
        $a->setObserv('obs');
        return $a;
    }

    private const OPTS = ['mi_sfsv' => 1, 'perm_vcsd' => false, 'perm_des' => false, 'perm_sg' => false, 'perm_admin' => false, 'is_dmz' => false];

    public function test_varios_estados_se_filtran_con_in(): void
    {
        $this->devolver([]);
        $this->uc()->execute(['status' => ['1', '2'], 'periodo' => 'otro', 'empiezamin' => '1/3/2027', 'empiezamax' => '31/3/2027'], self::OPTS);
        $this->assertSame(['status in [1,2]', 'id_tipo_activ matches ^1.....', 'f_ini between [2027-03-01,2027-03-31]'], $this->consultas[0]);
        $this->uc()->execute(['status' => '2'], self::OPTS);
        $this->assertSame('status eq 2', $this->consultas[1][0]);
    }

    public function test_tipo_por_secciones_para_sg_y_sr_y_tipo_explicito(): void
    {
        $this->devolver([]);
        $this->uc()->execute(['que' => 'list_activ_sr', 'seccion' => ['1'], 'asist' => ['7'], 'c_activ' => ['1', '3']], self::OPTS);
        $this->assertContains('id_tipo_activ matches ^(171...|173...)', $this->consultas[0]);
        $this->uc()->execute(['id_tipo_activ' => '1421', 'id_ubi' => '10200', 'dl_org' => 'dlp', 'periodo' => 'desdeHoy'], self::OPTS);
        $this->assertSame('id_tipo_activ matches ^1421', $this->consultas[1][0]);
        $this->assertStringStartsWith('f_fin between', $this->consultas[1][2]);
        $this->assertSame(['id_ubi eq 10200', 'dl_org eq dlp'], [$this->consultas[1][1], $this->consultas[1][3]]);
    }

    public function test_titulo_del_formulario_en_los_listados_de_sg_y_sr(): void
    {
        $this->devolver([]);
        $this->assertSame('Cursos de retiro de sg', $this->uc()->execute(['que' => 'list_activ_inv_sg', 'titulo' => 'cursos de retiro de sg'], self::OPTS)['titulo']);
        $this->assertSame('Listado de actividades', $this->uc()->execute(['que' => 'otra', 'titulo' => 'x'], self::OPTS)['titulo']);
    }

    public function test_listado_completo_con_todas_las_columnas(): void
    {
        $this->devolver([$this->actividad(10)]);
        $casa = new Casa();
        $casa->setId_ubi(10200);
        $casa->setNombre_ubi('Casa Uno');
        $casa->setSv(true);
        $casa->setSf(true);
        $this->casas->method('findById')->willReturn($casa);
        $t = new TipoTarifa();
        $t->setLetraVo('B');
        $this->tarifas->method('findById')->with(4)->willReturn($t);
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $c = new CentroDl();
        $c->setNombre_ubi('Ctr A');
        $this->encargados->method('getCentrosEncargadosActividad')->willReturn([$c]);
        $this->cargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $p = new PersonaDl();
        $p->setNom('Juan');
        $p->setApellido1('García');
        $this->cargos->method('getActividadSacds')->willReturn([$p]);

        $out = $this->uc()->execute(['que' => 'list_activ_compl'], ['perm_vcsd' => true] + self::OPTS);

        $this->assertSame([1, 1, 1], [$out['ver_hora'], $out['ver_tarifa'], $out['ver_sacd']]);
        $fila = $out['a_valores'][1];
        $this->assertSame('comun', $fila[1]);
        $this->assertSame(['20:30', '10:15'], [$fila[3], $fila[5]], 'las horas completas, no solo la hora');
        $this->assertSame(['sv', 'ca', 'n', 'cuadrienio', 'Casa Uno', 'B', 'Ctr A', 'obs'], [$fila[6], $fila[7], $fila[8], $fila[9], $fila[10], $fila[11], $fila[12], $fila[14]]);
        $this->assertStringContainsString('García', $fila[13]);
        $this->assertSame(['id_pau' => 10, 'que' => 'list_activ_compl'], $fila[15]['link_spec']['query']);
        $this->assertSame('Común', $out['a_cabeceras'][0]);
    }

    public function test_sg_sin_admin_no_ve_tarifa_ni_sacd_y_en_dmz_no_hay_enlace(): void
    {
        $this->devolver([$this->actividad(10, 112000)]);
        $this->casas->method('findById')->willReturn(null);
        $out = $this->uc()->execute(['que' => 'list_activ_inv_sg'], ['perm_sg' => true, 'is_dmz' => true] + self::OPTS);
        $this->assertSame([1, 0, 0], [$out['ver_hora'], $out['ver_tarifa'], $out['ver_sacd']]);
        $fila = $out['a_valores'][1];
        $this->assertSame('', $fila[9], 'sg no ve «(sin especificar)»');
        $this->assertArrayNotHasKey(11, $fila);
        $this->assertArrayNotHasKey(13, $fila);
        $this->assertArrayNotHasKey(15, $fila);

        $admin = $this->uc()->execute(['que' => 'list_activ_inv_sg'], ['perm_sg' => true, 'perm_admin' => true] + self::OPTS);
        $this->assertSame([1, 1], [$admin['ver_tarifa'], $admin['ver_sacd']]);
        $this->assertSame('(sin especificar)', $admin['a_valores'][1][9]);
    }

    public function test_casa_solo_de_sv_o_de_sf(): void
    {
        $this->devolver([$this->actividad(10)]);
        $casa = new Casa();
        $casa->setId_ubi(10200);
        $casa->setNombre_ubi('Casa');
        $casa->setSv(false);
        $casa->setSf(true);
        $this->casas->method('findById')->willReturn($casa);
        $this->assertSame('sf', $this->uc()->execute(['que' => 'list_activ_compl'], self::OPTS)['a_valores'][1][1]);
    }
}
