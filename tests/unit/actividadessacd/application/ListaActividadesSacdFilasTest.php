<?php

declare(strict_types=1);

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadessacd\application\ListaActividadesSacdData;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XPermisos;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\entity\ActividadFase;
use src\procesos\domain\PermAccion;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;
use Tests\support\DescribeCriteria;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Actividades con sus sacd: filtro por tipo, filas con centros y sacd (y sus
 * cápsulas), clase según aprobación/proyecto, permisos y el listado «falta sacd».
 */
final class ListaActividadesSacdFilasTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private ActividadDlRepositoryInterface&MockObject $actividades;
    private ActividadCargoRepositoryInterface&MockObject $cargos;
    private CentroEncargadoRepositoryInterface&MockObject $encargados;
    private ActividadFaseRepositoryInterface&MockObject $fases;
    private ActividadProcesoTareaRepositoryInterface&MockObject $procesos;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var list<list<string>> */
    private array $consultas = [];
    /** @var array<int, list<int>> sacd (id_nom) por actividad */
    private array $sacdPorActividad = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['config', 'oPerm', 'oPermActividades'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $_SESSION['config'] = ['a_apps' => [], 'app_installed' => []];
        $this->actividades = $this->createMock(ActividadDlRepositoryInterface::class);
        $this->cargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $this->cargos->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            $id = (int) substr($f[0], strlen('id_activ eq '));
            $this->assertSame('id_cargo in [35,36]', $f[1]);
            $this->assertSame(['id_cargo desc'], DescribeCriteria::orden($c));
            return array_map(function (int $nom) use ($id): ActividadCargo {
                $c = new ActividadCargo();
                $c->setId_activ($id);
                $c->setId_cargo(35);
                $c->setId_nom($nom);
                return $c;
            }, $this->sacdPorActividad[$id] ?? []);
        });
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->encargados->method('getCentrosEncargadosActividad')->willReturn([]);
        $this->fases = $this->createMock(ActividadFaseRepositoryInterface::class);
        $this->procesos = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(function (int $id): ?PersonaDl {
            if ($id === 0) {
                return null;
            }
            $p = new PersonaDl();
            $p->setNom('Sacd');
            $p->setApellido1((string) $id);
            return $p;
        });
        $this->sustituirServicio(PersonaFinderService::class, $finder);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    private function uc(): ListaActividadesSacdData
    {
        $cargosSacd = $this->createMock(CargoRepositoryInterface::class);
        $cargosSacd->method('getArrayCargos')->with('sacd')->willReturn([35 => 'sacd', 36 => 'sacd2']);
        return new ListaActividadesSacdData($this->actividades, $cargosSacd, $this->cargos, $this->encargados, $this->fases, $this->procesos);
    }

    private function actividad(int $id, int $status = 2): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setId_tipo_activ(141000);
        $a->setNom_activ("crt $id");
        $a->setDl_org('dlb');
        $a->setStatus($status);
        $a->setF_ini(new DateTimeLocal('2027-03-05'));
        $a->setF_fin(new DateTimeLocal('2027-03-08'));
        return $a;
    }

    /** @param list<ActividadAll> $lista */
    private function devolver(array $lista): void
    {
        $this->actividades->method('matching')->willReturnCallback(function ($c) use ($lista): array {
            $this->consultas[] = DescribeCriteria::filtros($c);
            return $lista;
        });
    }

    public static function providerTipos(): \Generator
    {
        foreach (['sv' => '^1', 'na' => '^1[13]', 'sg' => '^1[45]', 'sr' => '^17', 'sssc' => '^16', 'sf' => '^2', 'sf_na' => '^2[123]', 'sf_sg' => '^2[45]', 'sf_sr' => '^27'] as $t => $re) {
            yield $t => [$t, "id_tipo_activ matches $re"];
        }
        yield 'otro: sin filtro de tipo' => ['otro', null];
    }

    #[DataProvider('providerTipos')]
    public function test_filtro_por_tipo(string $tipo, ?string $filtro): void
    {
        $this->devolver([]);
        $this->uc()->execute(['tipo' => $tipo, 'periodo' => 'otro', 'empiezamin' => '1/3/2027', 'empiezamax' => '31/3/2027']);
        $esperado = ['f_ini between [2027-03-01,2027-03-31]', 'status lt 3'];
        if ($filtro !== null) {
            $esperado[] = $filtro;
        }
        $this->assertSame($esperado, $this->consultas[0]);
    }

    public function test_fila_con_centros_y_sacd_sin_procesos(): void
    {
        $this->devolver([$this->actividad(10)]);
        $c = new CentroDl();
        $c->setNombre_ubi('Ctr A');
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->encargados->method('getCentrosEncargadosActividad')->with(10)->willReturn([$c]);
        $this->sacdPorActividad = [10 => [20, 0]];
        $oPerm = $this->createMock(XPermisos::class);
        $oPerm->method('have_perm_oficina')->willReturn(true);
        $_SESSION['oPerm'] = $oPerm;

        $out = $this->uc()->execute(['tipo' => 'sg']);

        $fila = $out['filas'][0];
        $this->assertSame(['crt 10 [Ctr A]', 'plaza4', true, true], [$fila['nom_activ'], $fila['clase'], $fila['perm_modificar'], $fila['perm_crear']]);
        $this->assertSame([20, 0], array_column($fila['sacds'], 'id_nom'));
        $this->assertStringContainsString('20', $fila['sacds'][0]['ap_nom']);
        $this->assertSame('', $fila['sacds'][1]['ap_nom'], 'persona no encontrada');
        $this->assertSame(['id_activ' => 10, 'id_nom' => 20, 'id_cargo' => 35], HashB::open($fila['sacds'][0]['ctx_eliminar'], 'sacd_eliminar'));
        $this->assertSame(['id_activ' => 10, 'id_nom' => 20], HashB::open($fila['sacds'][0]['ctx_reordenar'], 'sacd_reordenar'));
        $this->assertTrue($out['perm_des']);
        $this->assertFalse($out['mostrar_nota_falta_sacd']);
    }

    private function conProcesos(array $permisos, array $aprobado): void
    {
        $_SESSION['config'] = ['a_apps' => ['procesos' => 3], 'app_installed' => [3]];
        $actual = 0;
        $perm = $this->createMock(PermisosActividades::class);
        $perm->method('setActividad')->willReturnCallback(function (int $id) use (&$actual): void {
            $actual = $id;
        });
        $perm->method('getPermisoActual')->willReturnCallback(function (string $que) use (&$actual, $permisos): PermAccion {
            $lista = $permisos[$actual][$que] ?? ['ocupado', 'ver', 'modificar'];
            $a = $this->createMock(PermAccion::class);
            $a->method('have_perm_activ')->willReturnCallback(fn (string $p): bool => in_array($p, $lista, true));
            return $a;
        });
        $_SESSION['oPermActividades'] = $perm;
        $this->procesos->method('getSacdAprobado')->willReturnCallback(fn (int $id): ?bool => $aprobado[$id] ?? false);
    }

    public function test_con_procesos_permisos_clase_y_sacd_ocultos(): void
    {
        $this->conProcesos([
            11 => ['datos' => ['ocupado']],
            12 => ['datos' => []],
            13 => ['ctr' => [], 'sacd' => []],
        ], [10 => true]);
        $this->devolver([$this->actividad(10), $this->actividad(11), $this->actividad(12), $this->actividad(13, 1)]);
        $this->sacdPorActividad = [10 => [20], 13 => [21]];

        $filas = $this->uc()->execute(['tipo' => 'sg'])['filas'];

        $this->assertSame([10, 13], array_column($filas, 'id_activ'));
        $this->assertSame('plaza4', $filas[0]['clase']);
        $this->assertSame('wrong-soft', $filas[1]['clase'], 'en proyecto');
        $this->assertSame([], $filas[1]['sacds'], 'sin permiso de ver sacd');
        $this->assertFalse($filas[1]['perm_modificar']);
    }

    public function test_falta_sacd_las_que_no_tienen_sacd_y_las_que_lo_tienen_sin_aprobar(): void
    {
        $fase = new ActividadFase();
        $fase->setDesc_fase('ok sacd');
        $this->fases->method('findById')->willReturn($fase);
        // 1: con sacd y aprobada (fuera); 2: con sacd sin aprobar; 3: sin sacd aprobada; 4: sin sacd sin aprobar.
        $this->conProcesos([], [1 => true, 3 => true]);
        $this->devolver([$this->actividad(1), $this->actividad(2), $this->actividad(3), $this->actividad(4)]);
        $this->sacdPorActividad = [1 => [20], 2 => [21]];

        $out = $this->uc()->execute(['tipo' => 'falta_sacd']);

        $this->assertSame([2, 3, 4], array_column($out['filas'], 'id_activ'));
        $this->assertSame('ok sacd', $out['texto_fase_ok_sacd']);
        $this->assertTrue($out['mostrar_nota_falta_sacd']);
        $this->assertCount(2, $this->consultas[0], 'falta_sacd no filtra por tipo');
    }
}
