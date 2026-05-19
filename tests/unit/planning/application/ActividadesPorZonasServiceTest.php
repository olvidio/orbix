<?php

namespace Tests\unit\planning\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use src\planning\application\ActividadesPorZonasService;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\Zona;

/**
 * Unitarios de {@see ActividadesPorZonasService::execute}. Por la enorme
 * superficie de dependencias (activcargos, encargos, permisos, etc.),
 * aqui se cubren:
 *  - Seleccion de una zona concreta por id.
 *  - Modo `todo_propias` cuando no hay zonas propias.
 *  - El calculo del rango de fechas segun el trimestre (1..6).
 *  - El titulo segun el numero de zonas iteradas.
 *  - Que las sacds con situacion distinta de `A` se ignoren.
 *  - Que cuando `Qactividad` != 'si' se agreguen personas con lista vacia.
 */
final class ActividadesPorZonasServiceTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // `src\shared\domain\helpers\is_true` vive en func_tablas.php; el bootstrap solo carga el
        // global_header, por lo que lo cargamos explicitamente aqui.
        require_once __DIR__ . '/../../../../src/shared/domain/helpers/func_tablas.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        // Evita que `ConfigGlobal::is_app_installed('procesos')` sea true y
        // dispare la cascada de permisos dentro de `actividadesDeSacd`.
        $_SESSION['config'] = ['a_apps' => [], 'app_installed' => []];
        // `DateTimeLocal::getFromLocal` resuelve el formato mirando el
        // idioma de la sesion; lo fijamos para evitar notices en los tests.
        $_SESSION['session_auth']['idioma'] = 'es_ES.UTF8';
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_una_zona_sin_sacds_devuelve_fila_vacia_y_titulo_es_el_nombre_de_la_zona(): void
    {
        $oZona = $this->zona('Zona Alfa');

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->with('55')->willReturn($oZona);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap($this->defaultServices([
            ZonaRepositoryInterface::class => $zonaRepo,
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
        ]));

        $out = ActividadesPorZonasService::execute('55', 1, 2030, 'si', 'no');

        $this->assertSame(1, $out['zonas']);
        $this->assertSame('Zona Alfa', $out['titulo']);
        $this->assertSame(['Zona Alfa'], array_values($out['cabeceras_por_zona']));
        // Una unica fila en blanco `###`.
        $this->assertCount(1, $out['actividades_por_zona'][1]);
    }

    public function test_todo_propias_cuando_no_hay_zonas_propias_devuelve_cero_zonas(): void
    {
        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->expects($this->never())->method('findById');

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')
            ->with(['propia' => 't'])
            ->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap($this->defaultServices([
            ZonaRepositoryInterface::class => $zonaRepo,
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
        ]));

        $out = ActividadesPorZonasService::execute('todo_propias', 1, 2030, 'si', 'no');

        $this->assertSame(0, $out['zonas']);
        $this->assertSame([], $out['actividades_por_zona']);
    }

    public function test_trimestre_1_usa_rango_de_enero_a_marzo(): void
    {
        $oZona = $this->zona('Z');

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($oZona);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap($this->defaultServices([
            ZonaRepositoryInterface::class => $zonaRepo,
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
        ]));

        $out = ActividadesPorZonasService::execute('99', 1, 2030, 'no', 'no');

        $this->assertSame('2030-01-01', $out['oIniPlanning']->format('Y-m-d'));
        $this->assertSame('2030-03-31', $out['oFinPlanning']->format('Y-m-d'));
    }

    public function test_trimestre_5_cruza_anyos(): void
    {
        $oZona = $this->zona('Z');

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($oZona);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap($this->defaultServices([
            ZonaRepositoryInterface::class => $zonaRepo,
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
        ]));

        $out = ActividadesPorZonasService::execute('99', 5, 2030, 'no', 'no');

        $this->assertSame('2030-12-01', $out['oIniPlanning']->format('Y-m-d'));
        $this->assertSame('2031-01-31', $out['oFinPlanning']->format('Y-m-d'));
    }

    public function test_trimestre_mensual_101_usa_enero(): void
    {
        $oZona = $this->zona('Z');

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($oZona);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap($this->defaultServices([
            ZonaRepositoryInterface::class => $zonaRepo,
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
        ]));

        $out = ActividadesPorZonasService::execute('99', 101, 2030, 'no', 'no');

        $this->assertSame('2030-01-01', $out['oIniPlanning']->format('Y-m-d'));
        $this->assertSame('2030-01-31', $out['oFinPlanning']->format('Y-m-d'));
    }

    public function test_sacd_inexistente_en_personas_se_omite(): void
    {
        $oZona = $this->zona('Z');

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($oZona);

        $zonaSacd = new class {
            public function getId_nom(): int { return 9999; }
            public function getId_zona(): int { return 99; }
        };
        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')->willReturn([$zonaSacd]);

        $sacdRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $sacdRepo->method('findById')->with(9999)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap($this->defaultServices([
            ZonaRepositoryInterface::class => $zonaRepo,
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
            PersonaSacdRepositoryInterface::class => $sacdRepo,
        ]));

        $out = ActividadesPorZonasService::execute('99', 1, 2030, 'si', 'no');

        $this->assertCount(1, $out['actividades_por_zona'][1]);
    }

    public function test_sacd_con_situacion_distinta_de_A_se_omite(): void
    {
        $oZona = $this->zona('Z');

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($oZona);

        $zonaSacd = new class {
            public function getId_nom(): int { return 7777; }
            public function getId_zona(): int { return 99; }
        };
        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')->willReturn([$zonaSacd]);

        $oSacd = $this->createStub(PersonaSacd::class);
        $oSacd->method('getSituacion')->willReturn('B'); // baja
        $oSacd->method('getPrefApellidosNombre')->willReturn('NO SE ESPERA');
        $sacdRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $sacdRepo->method('findById')->willReturn($oSacd);

        $GLOBALS['container'] = $this->containerFromMap($this->defaultServices([
            ZonaRepositoryInterface::class => $zonaRepo,
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
            PersonaSacdRepositoryInterface::class => $sacdRepo,
        ]));

        $out = ActividadesPorZonasService::execute('99', 1, 2030, 'si', 'no');

        // Debe quedar unicamente la fila en blanco "###".
        $this->assertCount(1, $out['actividades_por_zona'][1]);
    }

    public function test_qactividad_distinto_de_si_incluye_persona_con_lista_vacia(): void
    {
        $oZona = $this->zona('Z');

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($oZona);

        $zonaSacd = new class {
            public function getId_nom(): int { return 123; }
            public function getId_zona(): int { return 99; }
        };
        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')->willReturn([$zonaSacd]);

        $oSacd = $this->createStub(PersonaSacd::class);
        $oSacd->method('getSituacion')->willReturn('A');
        $oSacd->method('getPrefApellidosNombre')->willReturn('Sacd Uno');

        $sacdRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $sacdRepo->method('findById')->with(123)->willReturn($oSacd);

        $GLOBALS['container'] = $this->containerFromMap($this->defaultServices([
            ZonaRepositoryInterface::class => $zonaRepo,
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
            PersonaSacdRepositoryInterface::class => $sacdRepo,
        ]));

        $out = ActividadesPorZonasService::execute('99', 1, 2030, 'no', 'no');

        $actividades = $out['actividades_por_zona'][1];
        // uksort alfabetico: Sacd Uno antes que ###.
        $this->assertArrayHasKey('Sacd Uno', $actividades);
        $this->assertSame(['p#123#Sacd Uno' => []], $actividades['Sacd Uno']);
    }

    /**
     * Regresion: `ActividadesPorZonasService::actividadesDeSacd` castea
     * `h_ini` / `h_fin` a string (lineas 279 y 282). Como `TimeLocal`
     * hereda de `DateTimeLocal` (y esta de `DateTime`) sin `__toString`,
     * un cast directo revienta con:
     *   "Object of class TimeLocal could not be converted to string".
     *
     * Antes de este test, la suite cubria los caminos con
     * `getAsistenteCargoDeActividad` vacio, asi que nunca se llegaba al
     * bucle que procesa actividades. Al forzar una actividad con
     * horas reales, reproducimos el fallo observado en produccion.
     */
    public function test_actividad_con_TimeLocal_como_horario_no_falla_al_convertir_a_string(): void
    {
        $oZona = $this->zona('Z');

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($oZona);

        $zonaSacd = new class {
            public function getId_nom(): int { return 123; }
            public function getId_zona(): int { return 99; }
        };
        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')->willReturn([$zonaSacd]);

        $oSacd = $this->createStub(PersonaSacd::class);
        $oSacd->method('getSituacion')->willReturn('A');
        $oSacd->method('getPrefApellidosNombre')->willReturn('Sacd Uno');
        $sacdRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $sacdRepo->method('findById')->willReturn($oSacd);

        $oActividad = $this->createStub(ActividadAll::class);
        $oActividad->method('getId_activ')->willReturn(1001);
        // 141 = sv(1) + s(4) + crt(1); valido para `TiposActividades::separarId`.
        $oActividad->method('getId_tipo_activ')->willReturn(141);
        $oActividad->method('getF_ini')->willReturn(new DateTimeLocal('2030-02-15'));
        $oActividad->method('getF_fin')->willReturn(new DateTimeLocal('2030-02-15'));
        $oActividad->method('getH_ini')->willReturn(TimeLocal::fromString('10:30:00'));
        $oActividad->method('getH_fin')->willReturn(TimeLocal::fromString('12:30:00'));
        $oActividad->method('getDl_org')->willReturn('');
        $oActividad->method('getNom_activ')->willReturn('Curso test');
        $oActividad->method('getStatus')->willReturn(0);

        $activRepo = $this->createMock(ActividadRepositoryInterface::class);
        $activRepo->method('getActividades')->willReturn([$oActividad]);

        $actCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $actCargoRepo->method('getAsistenteCargoDeActividad')->willReturn([
            ['id_activ' => 1001, 'propio' => 't', 'plaza' => 0, 'id_cargo' => ''],
        ]);

        $encargoSacdRepo = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $encargoSacdRepo->method('getEncargoSacdHorarios')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap($this->defaultServices([
            ZonaRepositoryInterface::class => $zonaRepo,
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
            PersonaSacdRepositoryInterface::class => $sacdRepo,
            ActividadRepositoryInterface::class => $activRepo,
            ActividadCargoRepositoryInterface::class => $actCargoRepo,
            EncargoSacdHorarioRepositoryInterface::class => $encargoSacdRepo,
            TipoDeActividadRepositoryInterface::class => $this->createStub(TipoDeActividadRepositoryInterface::class),
        ]));

        // `Qpropuesta = 'true'` (reconocido por `is_true`) desvia a la rama
        // que no toca `$_SESSION['oPermActividades']`.
        $out = ActividadesPorZonasService::execute('99', 1, 2030, 'si', 'true');

        $actividades = $out['actividades_por_zona'][1]['Sacd Uno']['p#123#Sacd Uno'];
        $this->assertCount(1, $actividades);
        $this->assertSame('10:30', $actividades[0]['h_ini']);
        $this->assertSame('12:30', $actividades[0]['h_fi']);
    }

    public function test_year_vacio_usa_el_siguiente_anyo(): void
    {
        $oZona = $this->zona('Z');

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($oZona);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap($this->defaultServices([
            ZonaRepositoryInterface::class => $zonaRepo,
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
        ]));

        $out = ActividadesPorZonasService::execute('99', 1, 0, 'no', 'no');

        $this->assertSame(
            (int)date('Y') + 1,
            (int)$out['oIniPlanning']->format('Y')
        );
    }

    /**
     * @param array<class-string, object> $overrides
     * @return array<class-string, object>
     */
    private function defaultServices(array $overrides = []): array
    {
        $defaults = [
            CargoRepositoryInterface::class => $this->createStub(CargoRepositoryInterface::class),
            ActividadRepositoryInterface::class => $this->createStub(ActividadRepositoryInterface::class),
            PersonaSacdRepositoryInterface::class => $this->createStub(PersonaSacdRepositoryInterface::class),
            EncargoRepositoryInterface::class => $this->createStub(EncargoRepositoryInterface::class),
            EncargoSacdHorarioRepositoryInterface::class => $this->createStub(EncargoSacdHorarioRepositoryInterface::class),
            ActividadCargoRepositoryInterface::class => $this->createStub(ActividadCargoRepositoryInterface::class),
            ZonaRepositoryInterface::class => $this->createStub(ZonaRepositoryInterface::class),
            ZonaSacdRepositoryInterface::class => $this->createStub(ZonaSacdRepositoryInterface::class),
        ];
        return array_merge($defaults, $overrides);
    }

    private function zona(string $nombre): Zona
    {
        $stub = $this->createStub(Zona::class);
        $stub->method('getNombre_zona')->willReturn($nombre);
        $stub->method('getOrden')->willReturn(1);
        return $stub;
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
