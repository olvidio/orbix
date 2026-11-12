<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\application\ActividadNueva;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\entity\Importada;
use src\actividades\domain\value_objects\StatusId;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\permisos\domain\PermisosActividades;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Alta de una actividad: tabla dl o ex según el organizador, dl que ya usa
 * Orbix, permisos de creación (procesos), campos obligatorios, valores
 * grabados, importada y plazas de la dl (actividadplazas).
 *
 * Extiende myTest porque la comprobación «dl que ya usa Orbix» lee los
 * esquemas de la BD comun (solo lectura).
 */
final class ActividadNuevaTest extends myTest
{
    private ActividadDlRepositoryInterface&MockObject $dl;
    private ActividadExRepositoryInterface&MockObject $ex;
    private ImportadaRepositoryInterface&MockObject $importadas;
    private DelegacionRepositoryInterface&MockObject $delegaciones;
    private ActividadPlazasDlRepositoryInterface&MockObject $plazas;
    /** @var array<string, mixed> */
    private array $anterior = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['config', 'oPermActividades', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $_SESSION['session_auth'] = array_merge(
            is_array($this->anterior['session_auth']) ? $this->anterior['session_auth'] : [],
            ['sfsv' => 1, 'esquema' => 'H-dlbv', 'idioma' => 'es_ES.UTF-8']
        );
        $_SESSION['config'] = ['a_apps' => [], 'app_installed' => []];
        $this->dl = $this->createMock(ActividadDlRepositoryInterface::class);
        $this->ex = $this->createMock(ActividadExRepositoryInterface::class);
        $this->importadas = $this->createMock(ImportadaRepositoryInterface::class);
        $this->delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $this->plazas = $this->createMock(ActividadPlazasDlRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    private function uc(): ActividadNueva
    {
        return new ActividadNueva($this->dl, $this->ex, $this->importadas, $this->delegaciones, $this->plazas);
    }

    /** @param list<string> $nombres */
    private function apps(array $nombres): void
    {
        $ids = ['procesos' => 3, 'actividadplazas' => 4];
        $a = array_intersect_key($ids, array_flip($nombres));
        $_SESSION['config'] = ['a_apps' => $a, 'app_installed' => array_values($a)];
    }

    /** @return array<string, mixed> */
    private function datos(array $cambios = []): array
    {
        return array_merge([
            'dl_org' => 'dlb',
            'publicado' => 'false',
            'id_tipo_activ' => '112021',
            'nom_activ' => 'ca n (1/7/2027-20/7/2027)',
            'f_ini' => '1/7/2027',
            'f_fin' => '20/7/2027',
            'status' => '2',
            'id_ubi' => '0',
            'lugar_esp' => 'Montaña',
            'desc_activ' => 'descripción',
            'precio' => '350,50',
            'num_asistentes' => '25',
            'observ' => 'observaciones',
            'nivel_stgr' => '',
            'id_repeticion' => '11',
            'observ_material' => 'proyector',
            'tarifa' => '4',
            'h_ini' => '20:30',
            'h_fin' => '',
            'plazas' => '',
            'idioma' => '',
        ], $cambios);
    }

    private function guardarEnDl(?ActividadAll &$g, bool $ok = true): void
    {
        $this->dl->method('getNewId')->willReturn(40);
        $this->dl->method('getNewIdActividad')->with(40)->willReturn(3040);
        $this->dl->expects($this->once())->method('Guardar')
            ->willReturnCallback(function (ActividadAll $a) use (&$g, $ok): bool {
                $g = $a;
                return $ok;
            });
        $this->ex->expects($this->never())->method('Guardar');
    }

    public function test_alta_de_mi_dl_graba_todos_los_campos_en_la_tabla_dl(): void
    {
        $this->guardarEnDl($g);
        $this->importadas->expects($this->never())->method('Guardar');

        $this->assertSame('3040', $this->uc()->actividadNueva($this->datos()));

        $this->assertSame(3040, $g->getId_activ());
        $this->assertSame('dl', $g->getId_tabla());
        $this->assertSame('dlb', $g->getDl_org());
        $this->assertSame(112021, $g->getId_tipo_activ());
        $this->assertSame('ca n (1/7/2027-20/7/2027)', $g->getNom_activ());
        $this->assertSame('2027-07-01', $g->getF_ini()->format('Y-m-d'));
        $this->assertSame('2027-07-20', $g->getF_fin()->format('Y-m-d'));
        $this->assertSame(StatusId::ACTUAL, $g->getStatus());
        $this->assertSame(0, $g->getId_ubi());
        $this->assertSame('Montaña', $g->getLugar_esp());
        $this->assertSame('descripción', $g->getDesc_activ());
        $this->assertSame(350.5, $g->getPrecio());
        $this->assertSame(25, $g->getNum_asistentes());
        $this->assertSame('observaciones', $g->getObserv());
        // Nivel stgr vacío: se deduce del tipo (cuadrienio).
        $this->assertSame(2, $g->getNivel_stgr());
        $this->assertSame(11, $g->getId_repeticion());
        $this->assertSame('proyector', $g->getObserv_material());
        $this->assertSame(4, $g->getTarifa());
        $this->assertSame('20:30:00', $g->getH_ini()->toDatabaseString());
        $this->assertNull($g->getH_fin());
        $this->assertFalse($g->isPublicado());
    }

    public function test_con_casa_se_vacia_el_lugar_especial_y_se_respeta_el_nivel_stgr(): void
    {
        $this->guardarEnDl($g);
        $this->uc()->actividadNueva($this->datos(['id_ubi' => '200', 'nivel_stgr' => '5', 'publicado' => 'true', 'idioma' => 'ca_ES']));
        $this->assertSame(200, $g->getId_ubi());
        $this->assertSame('', $g->getLugar_esp());
        $this->assertSame(5, $g->getNivel_stgr());
        $this->assertTrue($g->isPublicado());
        $this->assertSame('ca_ES', $g->getIdiomaVo()?->value());
    }

    public function test_lugar_sin_determinar_conserva_el_lugar_especial(): void
    {
        $this->guardarEnDl($g);
        $this->uc()->actividadNueva($this->datos(['id_ubi' => '1']));
        $this->assertSame(1, $g->getId_ubi());
        $this->assertSame('Montaña', $g->getLugar_esp());
    }

    public function test_actividad_de_sf_de_mi_dl_va_a_la_tabla_dl(): void
    {
        $this->guardarEnDl($g);
        $this->importadas->expects($this->never())->method('Guardar');
        $this->uc()->actividadNueva($this->datos(['dl_org' => 'dlbf', 'id_tipo_activ' => '212021']));
        $this->assertSame('dl', $g->getId_tabla());
        $this->assertSame('dlbf', $g->getDl_org());
    }

    public function test_dl_externa_va_a_la_tabla_ex_publicada_actual_e_importada(): void
    {
        $this->ex->method('getNewId')->willReturn(41);
        $this->ex->method('getNewIdActividad')->with(41)->willReturn(-3041);
        $this->ex->expects($this->once())->method('Guardar')
            ->willReturnCallback(function (ActividadAll $a) use (&$g): bool {
                $g = $a;
                return true;
            });
        $this->dl->expects($this->never())->method('Guardar');
        $this->importadas->expects($this->once())->method('Guardar')
            ->willReturnCallback(function (Importada $i): bool {
                $this->assertSame(-3041, $i->getId_activ());
                return true;
            });
        $this->apps(['actividadplazas']);
        $this->plazas->expects($this->never())->method('Guardar');

        $id = $this->uc()->actividadNueva($this->datos(['dl_org' => 'dlz', 'status' => '1', 'plazas' => '10']));

        $this->assertSame('-3041', $id);
        $this->assertSame('ex', $g->getId_tabla());
        $this->assertTrue($g->isPublicado());
        $this->assertSame(StatusId::ACTUAL, $g->getStatus());
    }

    public function test_dl_externa_si_falla_la_importada_avisa(): void
    {
        $this->ex->method('Guardar')->willReturn(true);
        $this->importadas->method('Guardar')->willReturn(false);
        $this->importadas->method('getErrorTxt')->willReturn('fallo imp');
        $this->expectExceptionMessage('hay un error, no se ha importado: fallo imp');
        $this->uc()->actividadNueva($this->datos(['dl_org' => 'dlz']));
    }

    public function test_no_se_puede_crear_una_actividad_de_una_dl_que_ya_usa_orbix(): void
    {
        $this->dl->expects($this->never())->method('Guardar');
        $this->ex->expects($this->never())->method('Guardar');
        $this->expectExceptionMessage('No puede crear una actividad que organiza una dl/r que ya usa aquinate');
        $this->uc()->actividadNueva($this->datos(['dl_org' => 'dlp']));
    }

    public function test_no_se_puede_crear_una_actividad_de_sf_de_una_dl_que_ya_usa_orbix(): void
    {
        $this->ex->expects($this->never())->method('Guardar');
        $this->expectExceptionMessage('No puede crear una actividad que organiza una dl/r que ya usa aquinate');
        $this->uc()->actividadNueva($this->datos(['dl_org' => 'dlpf', 'id_tipo_activ' => '212021']));
    }

    public function test_campos_obligatorios(): void
    {
        foreach (['nom_activ' => '', 'f_ini' => '', 'f_fin' => '', 'status' => '0', 'dl_org' => ''] as $campo => $vacio) {
            try {
                $this->uc()->actividadNueva($this->datos([$campo => $vacio]));
                $this->fail("sin $campo debería fallar");
            } catch (\RuntimeException $e) {
                $this->assertSame('debe llenar todos los campos que tengan un (*)', $e->getMessage(), $campo);
            }
        }
    }

    public function test_tipo_de_actividad_incorrecto(): void
    {
        $this->dl->expects($this->never())->method('Guardar');
        $this->expectExceptionMessage('tipo de actividad incorrecto');
        $this->uc()->actividadNueva($this->datos(['id_tipo_activ' => '1120']));
    }

    public function test_si_no_se_guarda_avisa_con_el_error_del_repositorio(): void
    {
        $this->guardarEnDl($g, false);
        $this->dl->method('getErrorTxt')->willReturn('violación');
        $this->expectExceptionMessage('hay un error, no se ha guardado: violación');
        $this->uc()->actividadNueva($this->datos());
    }

    public function test_con_procesos_sin_sesion_de_permisos(): void
    {
        $this->apps(['procesos']);
        unset($_SESSION['oPermActividades']);
        $this->expectExceptionMessage('sesión de permisos no disponible');
        $this->uc()->actividadNueva($this->datos());
    }

    public function test_con_procesos_sin_permiso_de_crear(): void
    {
        $this->apps(['procesos']);
        $perm = $this->createMock(PermisosActividades::class);
        $perm->expects($this->once())->method('setId_tipo_activ')->with('112021');
        $perm->expects($this->once())->method('getPermisoCrear')->with(true)->willReturn(false);
        $_SESSION['oPermActividades'] = $perm;
        $this->dl->expects($this->never())->method('Guardar');
        $this->expectExceptionMessage('No tiene permiso para crear una actividad de este tipo');
        $this->uc()->actividadNueva($this->datos());
    }

    public function test_con_procesos_el_permiso_de_una_dl_ajena_se_pide_como_no_propia(): void
    {
        $this->apps(['procesos']);
        $perm = $this->createMock(PermisosActividades::class);
        $perm->expects($this->once())->method('getPermisoCrear')->with(false)->willReturn(['of_responsable_txt' => '', 'status' => 2]);
        $_SESSION['oPermActividades'] = $perm;
        $this->ex->method('Guardar')->willReturn(true);
        $this->importadas->method('Guardar')->willReturn(true);
        $this->uc()->actividadNueva($this->datos(['dl_org' => 'dlz']));
    }

    public function test_con_procesos_y_permiso_crea_la_actividad(): void
    {
        $this->apps(['procesos']);
        $perm = $this->createMock(PermisosActividades::class);
        $perm->method('getPermisoCrear')->willReturn(['of_responsable_txt' => '', 'status' => 2]);
        $_SESSION['oPermActividades'] = $perm;
        $this->guardarEnDl($g);
        $this->assertSame('3040', $this->uc()->actividadNueva($this->datos()));
    }

    private function delegacion(int $id): Delegacion
    {
        $d = new Delegacion();
        $d->setId_dl($id);
        $d->setDl('dlb');
        return $d;
    }

    public function test_plazas_de_mi_dl_crean_la_fila_de_plazas(): void
    {
        $this->apps(['actividadplazas']);
        $this->guardarEnDl($g);
        $this->delegaciones->method('matching')->willReturnCallback(function ($c): array {
            $this->assertSame(['dl eq dlb'], DescribeCriteria::filtros($c));
            return [$this->delegacion(7)];
        });
        $this->plazas->method('matching')->willReturnCallback(function ($c): array {
            $this->assertSame(['id_activ eq 3040', 'id_dl eq 7', 'dl_tabla eq dlb'], DescribeCriteria::filtros($c));
            return [];
        });
        $this->plazas->expects($this->once())->method('Guardar')
            ->willReturnCallback(function (ActividadPlazas $p): bool {
                $this->assertSame([3040, 7, 'dlb', '30'], [$p->getId_activ(), $p->getId_dl(), $p->getDl_tabla(), $p->getPlazas()]);
                return true;
            });
        $this->uc()->actividadNueva($this->datos(['plazas' => '30']));
        $this->assertSame(30, $g->getPlazas());
    }

    public function test_plazas_actualiza_la_fila_existente_y_sin_delegacion_usa_id_cero(): void
    {
        $this->apps(['actividadplazas']);
        $this->guardarEnDl($g);
        $this->delegaciones->method('matching')->willReturn([]);
        $existente = new ActividadPlazas();
        $existente->setId_activ(3040);
        $existente->setId_dl(0);
        $existente->setDlTablaVo('dlb');
        $existente->setPlazas(5);
        $this->plazas->method('matching')->willReturnCallback(function ($c) use ($existente): array {
            $this->assertContains('id_dl eq 0', DescribeCriteria::filtros($c));
            return [$existente];
        });
        $this->plazas->expects($this->once())->method('Guardar')->with($this->identicalTo($existente))->willReturn(true);
        $this->uc()->actividadNueva($this->datos(['plazas' => '12']));
        $this->assertSame('12', $existente->getPlazas());
    }

    public function test_plazas_si_no_se_guardan_avisa(): void
    {
        $this->apps(['actividadplazas']);
        $this->guardarEnDl($g);
        $this->delegaciones->method('matching')->willReturn([$this->delegacion(7)]);
        $this->plazas->method('matching')->willReturn([]);
        $this->plazas->method('Guardar')->willReturn(false);
        $this->plazas->method('getErrorTxt')->willReturn('fallo plazas');
        $this->expectExceptionMessage('hay un error, no se ha guardado: fallo plazas');
        $this->uc()->actividadNueva($this->datos(['plazas' => '12']));
    }

    public function test_sin_plazas_o_sin_la_app_no_toca_las_plazas(): void
    {
        $this->guardarEnDl($g);
        $this->plazas->expects($this->never())->method('Guardar');
        $this->uc()->actividadNueva($this->datos(['plazas' => '12']));
    }
}
