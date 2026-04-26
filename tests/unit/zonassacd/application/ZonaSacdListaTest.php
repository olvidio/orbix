<?php

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaSacd;
use src\zonassacd\application\ZonaSacdLista;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\Zona;
use src\zonassacd\domain\entity\ZonaSacd;

/**
 * Unitarios para {@see ZonaSacdLista::execute()}.
 *
 * Cubrimos las dos ramas:
 *  - `'no'`   -> lista de sacds sin ninguna zona asignada.
 *  - default  -> sacds de la zona solicitada, ordenados por apellidos,
 *                con indicadores L..D (`Dw1..Dw7`) en `x`/`-`.
 *
 * El servicio resuelve `PersonaFinderService` via contenedor (a traves
 * de `Persona::findPersonaEnGlobal`), `ZonaRepositoryInterface::findById`
 * y `ZonaSacdRepositoryInterface::getZonasSacds`; el test fija los
 * nombres reales de esos metodos para detectar typos.
 */
final class ZonaSacdListaTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // `src\shared\domain\helpers\is_true` vive en func_tablas.php y lo usa `ZonaSacdLista`
        // para decidir los marcadores de dias de la semana.
        require_once __DIR__ . '/../../../../src/shared/domain/helpers/func_tablas.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        // `ConfigGlobal::mi_delef()` se usa en la rama `no` y lee el
        // esquema desde sesion (formato `region-dl{v|f}`).
        $_SESSION['session_auth']['esquema'] = 'b-bnv';
        $_SESSION['session_auth']['sfsv'] = 1;
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

    public function test_no_lista_solo_sacds_sin_zona_asignada(): void
    {
        $oPersona1 = $this->personaSacdStub(101, 'Alvarez, Ana', 'n');
        $oPersona2 = $this->personaSacdStub(102, 'Beltran, Juan', 'a');

        $personaRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personaRepo->expects($this->once())
            ->method('getPersonas')
            ->willReturn([$oPersona1, $oPersona2]);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')->willReturnMap([
            [['id_nom' => 101], [], []],
            // Beltran ya tiene una zona -> se excluye.
            [['id_nom' => 102], [], [$this->createStub(ZonaSacd::class)]],
        ]);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaSacdRepositoryInterface::class => $personaRepo,
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
        ]);

        $out = ZonaSacdLista::execute('no');

        $this->assertCount(1, $out['a_valores']);
        $first = reset($out['a_valores']);
        $this->assertSame(101, $first['sel']);
        $this->assertSame('Alvarez, Ana', $first[1]);
        $this->assertSame('n', $first[2]);
    }

    public function test_zona_concreta_ordena_por_apellidos_y_marca_dias(): void
    {
        $oZona = $this->zonaStub('Zona Norte');
        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->expects($this->once())
            ->method('findById')
            ->willReturn($oZona);

        $oZonaSacdA = $this->zonaSacdStub(501, propia: true,  dias: [true, false, true, false, false, false, false]);
        $oZonaSacdB = $this->zonaSacdStub(502, propia: false, dias: [false, true, false, false, false, false, false]);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->expects($this->once())
            ->method('getZonasSacds')
            ->with(['id_zona' => '5'], [])
            ->willReturn([$oZonaSacdA, $oZonaSacdB]);

        // `Persona::findPersonaEnGlobal` resuelve `PersonaFinderService`
        // por contenedor.
        $oPersonaA = $this->createStub(PersonaDl::class);
        $oPersonaA->method('getPrefApellidosNombre')->willReturn('Zzzz, Ultimo');
        $oPersonaB = $this->createStub(PersonaDl::class);
        $oPersonaB->method('getPrefApellidosNombre')->willReturn('Aaaa, Primero');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->willReturnMap([
            [501, $oPersonaA],
            [502, $oPersonaB],
        ]);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaSacdRepositoryInterface::class
                => $this->createStub(PersonaSacdRepositoryInterface::class),
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
            ZonaRepositoryInterface::class => $zonaRepo,
            PersonaFinderService::class => $finder,
        ]);

        $out = ZonaSacdLista::execute('5');

        $this->assertCount(2, $out['a_valores']);
        // Ordenados alfabeticamente por `ap_nom`.
        $ordenado = array_values($out['a_valores']);
        $this->assertSame('Aaaa, Primero', $ordenado[0][1]);
        $this->assertSame(502, $ordenado[0]['sel']);
        $this->assertSame('Zona Norte', $ordenado[0][2]);
        $this->assertFalse($ordenado[0][3]);
        // Dia 2 (martes) activo para el segundo sacd.
        $this->assertSame('x', $ordenado[0][5]);
        $this->assertSame('-', $ordenado[0][4]);

        $this->assertSame('Zzzz, Ultimo', $ordenado[1][1]);
        $this->assertTrue($ordenado[1][3]);
        // Dias 1 y 3 activos para el primer sacd.
        $this->assertSame('x', $ordenado[1][4]);
        $this->assertSame('x', $ordenado[1][6]);
    }

    public function test_zona_concreta_con_persona_no_encontrada_muestra_placeholder(): void
    {
        $zonaRepo = $this->createStub(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($this->zonaStub('Zona X'));

        $oZonaSacd = $this->zonaSacdStub(999, propia: false, dias: array_fill(0, 7, false));

        $zonaSacdRepo = $this->createStub(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')->willReturn([$oZonaSacd]);

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaSacdRepositoryInterface::class
                => $this->createStub(PersonaSacdRepositoryInterface::class),
            ZonaSacdRepositoryInterface::class => $zonaSacdRepo,
            ZonaRepositoryInterface::class => $zonaRepo,
            PersonaFinderService::class => $finder,
        ]);

        $out = ZonaSacdLista::execute('7');

        $this->assertCount(1, $out['a_valores']);
        $first = reset($out['a_valores']);
        $this->assertStringContainsString('999', $first[1]);
    }

    private function personaSacdStub(int $id_nom, string $pref, string $id_tabla): PersonaSacd
    {
        $stub = $this->createStub(PersonaSacd::class);
        $stub->method('getId_nom')->willReturn($id_nom);
        $stub->method('getPrefApellidosNombre')->willReturn($pref);
        $stub->method('getId_tabla')->willReturn($id_tabla);
        return $stub;
    }

    /**
     * @param array<int, bool> $dias Siete valores l..d.
     */
    private function zonaSacdStub(int $id_nom, bool $propia, array $dias): ZonaSacd
    {
        $stub = $this->createStub(ZonaSacd::class);
        $stub->method('getId_nom')->willReturn($id_nom);
        $stub->method('isPropia')->willReturn($propia);
        $stub->method('isDw1')->willReturn($dias[0]);
        $stub->method('isDw2')->willReturn($dias[1]);
        $stub->method('isDw3')->willReturn($dias[2]);
        $stub->method('isDw4')->willReturn($dias[3]);
        $stub->method('isDw5')->willReturn($dias[4]);
        $stub->method('isDw6')->willReturn($dias[5]);
        $stub->method('isDw7')->willReturn($dias[6]);
        return $stub;
    }

    private function zonaStub(string $nombre): Zona
    {
        $stub = $this->createStub(Zona::class);
        $stub->method('getNombre_zona')->willReturn($nombre);
        return $stub;
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
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
