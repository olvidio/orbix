<?php

namespace Tests\unit\planning\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\contracts\CargoOAsistenteInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\personas\domain\entity\PersonaGlobal;
use src\planning\application\ActividadesDePersonaService;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;

/**
 * Unitarios de {@see ActividadesDePersonaService::actividadesPorPersona}:
 * se centran en los casos "sin datos" y "agrupacion por centro vs lista
 * plana", que no requieren mockear la cascada completa de TiposActividades
 * ni los permisos por actividad.
 */
final class ActividadesDePersonaServiceTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        // Nos aseguramos de que `ConfigGlobal::is_app_installed('actividadcargos')`
        // devuelva false => el servicio no intentara invocar a CargoOAsistenteInterface.
        $_SESSION['config'] = ['a_apps' => [], 'app_installed' => []];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_cpersonas_no_array_con_agrupacion_devuelve_vacio(): void
    {
        $out = $this->createService()->actividadesPorPersona(
            false,
            '2030-03-31',
            '2030-01-01',
            $this->fecha('2030/1/1'),
            '1/1/2030',
            true,
        );

        $this->assertSame([], $out);
    }

    public function test_cpersonas_no_array_sin_agrupacion_devuelve_vacio(): void
    {
        $out = $this->createService()->actividadesPorPersona(
            false,
            '2030-03-31',
            '2030-01-01',
            $this->fecha('2030/1/1'),
            '1/1/2030',
            false,
        );

        $this->assertSame([], $out);
    }

    public function test_cpersonas_array_vacio_devuelve_vacio(): void
    {
        $out = $this->createService()->actividadesPorPersona(
            [],
            '2030-03-31',
            '2030-01-01',
            $this->fecha('2030/1/1'),
            '1/1/2030',
            true,
        );

        $this->assertSame([], $out);
    }

    public function test_sin_actividadcargos_agrupa_persona_sin_centro_como_centro_interrogante(): void
    {
        $oPersona = $this->createStub(PersonaGlobal::class);
        $oPersona->method('getId_nom')->willReturn(100);
        $oPersona->method('getPrefApellidosNombre')->willReturn('Fulanito de Tal');
        $oPersona->method('getId_ctr')->willReturn(null);

        $out = $this->createService()->actividadesPorPersona(
            [$oPersona],
            '2030-03-31',
            '2030-01-01',
            $this->fecha('2030/1/1'),
            '1/1/2030',
            true,
        );

        // Persona sin id_ctr -> agrupada bajo la etiqueta traducible "centro?".
        $this->assertCount(1, $out);
        $etiqueta = array_key_first($out);
        $this->assertNotEmpty($etiqueta);
        $this->assertSame([[
            "p#100#Fulanito de Tal#$etiqueta" => [],
        ]], $out[$etiqueta]);
    }

    public function test_sin_actividadcargos_usa_nombre_del_centro_cuando_hay_id_ctr(): void
    {
        $oPersona = $this->createStub(PersonaGlobal::class);
        $oPersona->method('getId_nom')->willReturn(200);
        $oPersona->method('getPrefApellidosNombre')->willReturn('Perengana Martinez');
        $oPersona->method('getId_ctr')->willReturn(777);

        $oCentroDl = $this->createStub(CentroDl::class);
        $oCentroDl->method('getNombre_ubi')->willReturn('Centro Alfa');

        $centroRepo = $this->createStub(CentroDlRepositoryInterface::class);
        $centroRepo->method('findById')->with(777)->willReturn($oCentroDl);

        $out = $this->createService(centroDl: $centroRepo)->actividadesPorPersona(
            [$oPersona],
            '2030-03-31',
            '2030-01-01',
            $this->fecha('2030/1/1'),
            '1/1/2030',
            true,
        );

        $this->assertArrayHasKey('Centro Alfa', $out);
        $this->assertSame([[
            'p#200#Perengana Martinez#Centro Alfa' => [],
        ]], $out['Centro Alfa']);
    }

    public function test_agrupacion_false_devuelve_lista_plana_sin_centro(): void
    {
        $oPersona1 = $this->createStub(PersonaGlobal::class);
        $oPersona1->method('getId_nom')->willReturn(1);
        $oPersona1->method('getPrefApellidosNombre')->willReturn('Uno Uno');

        $oPersona2 = $this->createStub(PersonaGlobal::class);
        $oPersona2->method('getId_nom')->willReturn(2);
        $oPersona2->method('getPrefApellidosNombre')->willReturn('Dos Dos');

        $out = $this->createService()->actividadesPorPersona(
            [$oPersona1, $oPersona2],
            '2030-03-31',
            '2030-01-01',
            $this->fecha('2030/1/1'),
            '1/1/2030',
            false,
        );

        $this->assertSame([
            ['p#1#Uno Uno' => []],
            ['p#2#Dos Dos' => []],
        ], $out);
    }

    public function test_varias_personas_en_el_mismo_centro_se_agrupan_juntas(): void
    {
        $oPersona1 = $this->createStub(PersonaGlobal::class);
        $oPersona1->method('getId_nom')->willReturn(1);
        $oPersona1->method('getPrefApellidosNombre')->willReturn('Primero');
        $oPersona1->method('getId_ctr')->willReturn(888);

        $oPersona2 = $this->createStub(PersonaGlobal::class);
        $oPersona2->method('getId_nom')->willReturn(2);
        $oPersona2->method('getPrefApellidosNombre')->willReturn('Segundo');
        $oPersona2->method('getId_ctr')->willReturn(888);

        $oCentroDl = $this->createStub(CentroDl::class);
        $oCentroDl->method('getNombre_ubi')->willReturn('Centro Unico');

        $centroRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroRepo->method('findById')->with(888)->willReturn($oCentroDl);

        $out = $this->createService(centroDl: $centroRepo)->actividadesPorPersona(
            [$oPersona1, $oPersona2],
            '2030-03-31',
            '2030-01-01',
            $this->fecha('2030/1/1'),
            '1/1/2030',
            true,
        );

        $this->assertArrayHasKey('Centro Unico', $out);
        $this->assertCount(2, $out['Centro Unico']);
    }

    public function test_con_actividadcargos_instalado_consulta_cargo_o_asistente(): void
    {
        // Simulamos que la app `actividadcargos` esta instalada.
        $_SESSION['config'] = [
            'a_apps' => ['actividadcargos' => 42],
            'app_installed' => [42],
        ];

        $oPersona = $this->createStub(PersonaGlobal::class);
        $oPersona->method('getId_nom')->willReturn(999);
        $oPersona->method('getPrefApellidosNombre')->willReturn('Con Cargos');
        $oPersona->method('getId_ctr')->willReturn(null);

        $cargoOAsistente = $this->createMock(CargoOAsistenteInterface::class);
        $cargoOAsistente->expects($this->once())
            ->method('getCargoOAsistente')
            ->willReturn([]); // sin cargos/asistentes -> sin actividades

        $out = $this->createService(cargoOAsistente: $cargoOAsistente)->actividadesPorPersona(
            [$oPersona],
            '2030-03-31',
            '2030-01-01',
            $this->fecha('2030/1/1'),
            '1/1/2030',
            false,
        );

        $this->assertSame([
            ['p#999#Con Cargos' => []],
        ], $out);
    }

    // ---- con actividades ------------------------------------------------------------------

    /** @param array<string, mixed> $cfg */
    private function conActividad(\src\actividades\domain\entity\ActividadAll|null $act, array $apps = ['actividadcargos' => 42]): ActividadesDePersonaService
    {
        $_SESSION['config'] = ['a_apps' => $apps, 'app_installed' => array_values($apps)];
        $c = new \src\actividadcargos\domain\entity\CargoOAsistente(3001);
        $c->setId_nom(999);
        $c->setPropio(true);
        $cargos = $this->createStub(CargoOAsistenteInterface::class);
        $cargos->method('getCargoOAsistente')->willReturn([$c]);
        $repo = $this->createStub(ActividadRepositoryInterface::class);
        $repo->method('matching')->willReturn($act === null ? [] : [$act]);
        return $this->createService($repo, null, $cargos);
    }

    private static function actividad(string $ini, string $fin): \src\actividades\domain\entity\ActividadAll
    {
        $a = new \src\actividades\domain\entity\ActividadAll();
        $a->setId_activ(3001);
        $a->setId_tipo_activ(112021);
        $a->setNom_activ('ca de prueba');
        $a->setF_ini(new DateTimeLocal($ini));
        $a->setF_fin(new DateTimeLocal($fin));
        $a->setH_ini(\src\shared\domain\value_objects\TimeLocal::fromString('10:30:00'));
        $a->setStatus(2);
        $a->setDl_org('dlb');
        return $a;
    }

    private function persona(): PersonaGlobal
    {
        $p = $this->createStub(PersonaGlobal::class);
        $p->method('getId_nom')->willReturn(999);
        $p->method('getPrefApellidosNombre')->willReturn('Con Cargos');
        return $p;
    }

    /** @return list<array<string, mixed>> */
    private function actividadesDe(ActividadesDePersonaService $s): array
    {
        $out = $s->actividadesPorPersona([$this->persona()], '2030-03-31', '2030-01-01', new DateTimeLocal('2030-01-01'), '1/1/2030', false);
        return $out[0]['p#999#Con Cargos'];
    }

    public function test_una_actividad_dentro_del_periodo(): void
    {
        $a = $this->actividadesDe($this->conActividad(self::actividad('2030-02-10', '2030-02-15')));
        $this->assertCount(1, $a);
        $this->assertSame([3001, 112021, '10:30', 'ca de prueba', true], [$a[0]['id_activ'], $a[0]['id_tipo_activ'], $a[0]['h_ini'], $a[0]['nom_llarg'], $a[0]['propio']]);
        $this->assertSame((new DateTimeLocal('2030-02-10'))->getFromLocal(), $a[0]['f_ini']);
        $this->assertNotSame('', $a[0]['nom_curt']);
    }

    public function test_una_actividad_que_empieza_antes_del_periodo_se_pinta_desde_el_inicio(): void
    {
        $a = $this->actividadesDe($this->conActividad(self::actividad('2029-12-28', '2030-01-03')));
        $this->assertSame(['1/1/2030', '1:16'], [$a[0]['f_ini'], $a[0]['h_ini']]);
    }

    public function test_sin_actividad_en_el_periodo_no_sale(): void
    {
        $this->assertSame([], $this->actividadesDe($this->conActividad(null)));
    }

    private function permisos(bool $ocupado, bool $ver): void
    {
        $perm = $this->createStub(\src\procesos\domain\PermAccion::class);
        $perm->method('have_perm_activ')->willReturnCallback(fn (string $a): bool => $a === \src\procesos\domain\PermAccionBits::OCUPADO ? $ocupado : $ver);
        $sesion = $this->createStub(\src\permisos\domain\PermisosActividades::class);
        $sesion->method('getPermisoActual')->willReturn($perm);
        $_SESSION['oPermActividades'] = $sesion;
    }

    public function test_con_procesos_sin_permiso_de_ocupado_no_sale_y_sin_ver_solo_la_seccion(): void
    {
        $apps = ['actividadcargos' => 42, 'procesos' => 43];
        $this->permisos(false, false);
        $this->assertSame([], $this->actividadesDe($this->conActividad(self::actividad('2030-02-10', '2030-02-15'), $apps)));

        $this->permisos(true, false);
        $a = $this->actividadesDe($this->conActividad(self::actividad('2030-02-10', '2030-02-15'), $apps));
        $this->assertStringNotContainsString('ca de prueba', $a[0]['nom_llarg'], 'no ve el nombre');
        $this->assertStringContainsString($a[0]['nom_curt'], $a[0]['nom_llarg']);

        $this->permisos(true, true);
        $a = $this->actividadesDe($this->conActividad(self::actividad('2030-02-10', '2030-02-15'), $apps));
        $this->assertSame('ca de prueba', $a[0]['nom_llarg']);
    }

    private function fecha(string $iso): DateTimeLocal
    {
        return DateTimeLocal::createFromFormat('Y/m/d', $iso);
    }

    private function createService(
        ?ActividadRepositoryInterface $actividadRepository = null,
        ?CentroDlRepositoryInterface $centroDl = null,
        ?CargoOAsistenteInterface $cargoOAsistente = null,
    ): ActividadesDePersonaService {
        return new ActividadesDePersonaService(
            $actividadRepository ?? $this->createStub(ActividadRepositoryInterface::class),
            $centroDl ?? $this->createStub(CentroDlRepositoryInterface::class),
            $cargoOAsistente ?? $this->createStub(CargoOAsistenteInterface::class),
        );
    }
}
