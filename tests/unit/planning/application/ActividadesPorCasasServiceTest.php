<?php

namespace Tests\unit\planning\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\planning\application\ActividadesPorCasasService;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\Casa;

/**
 * Unitarios del caso de uso {@see ActividadesPorCasasService}: cubre los
 * distintos valores de `Qcdc_sel` (1-9), el agrupado por `nombre_ubi` y
 * la llamada al repositorio de actividades con el rango de fechas.
 *
 * Se mockea el contenedor DI (patron equivalente al de `SacdAsignarTest`)
 * para evitar instanciar repositorios reales contra la BD.
 */
final class ActividadesPorCasasServiceTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousPost;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousPost = $_POST;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        $_POST = $this->previousPost;
        parent::tearDown();
    }

    public function test_sin_casas_devuelve_lista_vacia_con_linea_en_blanco_final(): void
    {
        $casaRepo = $this->createMock(CasaDlRepositoryInterface::class);
        $casaRepo->method('getCasas')->willReturn([]);

        $actividadRepo = $this->createMock(ActividadRepositoryInterface::class);
        $actividadRepo->expects($this->never())->method('actividadesDeUnaCasa');

        $GLOBALS['container'] = $this->containerFromMap([
            CasaDlRepositoryInterface::class => $casaRepo,
            ActividadRepositoryInterface::class => $actividadRepo,
        ]);

        [$sCdc, $aActividades] = ActividadesPorCasasService::actividadesPorCasas(
            1,
            $this->fecha('2030/1/1'),
            $this->fecha('2030/3/31'),
            0,
            $this->fecha('2030/3/31'),
            $this->fecha('2030/1/1'),
        );

        $this->assertSame('', $sCdc);
        // Solo debe existir la linea en blanco final ["##" => []].
        $this->assertCount(1, $aActividades);
        $last = end($aActividades);
        $this->assertSame(['##' => []], $last);
    }

    public function test_qcdc_sel_1_filtra_casas_sv_y_sf_verdaderas(): void
    {
        $capturado = null;
        $casaRepo = $this->createMock(CasaDlRepositoryInterface::class);
        $casaRepo->method('getCasas')
            ->willReturnCallback(function (array $where, array $op) use (&$capturado) {
                $capturado = ['where' => $where, 'op' => $op];
                return [];
            });

        $actividadRepo = $this->createMock(ActividadRepositoryInterface::class);

        $GLOBALS['container'] = $this->containerFromMap([
            CasaDlRepositoryInterface::class => $casaRepo,
            ActividadRepositoryInterface::class => $actividadRepo,
        ]);

        ActividadesPorCasasService::actividadesPorCasas(
            1,
            $this->fecha('2030/1/1'),
            $this->fecha('2030/3/31'),
            0,
            $this->fecha('2030/3/31'),
            $this->fecha('2030/1/1'),
        );

        $this->assertSame('t', $capturado['where']['sv']);
        $this->assertSame('t', $capturado['where']['sf']);
        $this->assertSame('nombre_ubi', $capturado['where']['_ordre']);
    }

    public function test_qcdc_sel_3_aplica_filtro_tipo_casa_cdc_cdr(): void
    {
        $capturado = null;
        $casaRepo = $this->createMock(CasaDlRepositoryInterface::class);
        $casaRepo->method('getCasas')
            ->willReturnCallback(function (array $where, array $op) use (&$capturado) {
                $capturado = ['where' => $where, 'op' => $op];
                return [];
            });

        $actividadRepo = $this->createMock(ActividadRepositoryInterface::class);

        $GLOBALS['container'] = $this->containerFromMap([
            CasaDlRepositoryInterface::class => $casaRepo,
            ActividadRepositoryInterface::class => $actividadRepo,
        ]);

        ActividadesPorCasasService::actividadesPorCasas(
            3,
            $this->fecha('2030/1/1'),
            $this->fecha('2030/3/31'),
            0,
            $this->fecha('2030/3/31'),
            $this->fecha('2030/1/1'),
        );

        $this->assertSame('cdcdl', $capturado['where']['tipo_ubi']);
        $this->assertSame('cdc|cdr', $capturado['where']['tipo_casa']);
        $this->assertSame('~', $capturado['op']['tipo_casa']);
    }

    public function test_qcdc_sel_6_agrega_centros_ellas_despues_de_casas(): void
    {
        $oCasa = $this->crearCasaStub(10, 'AlfaCasa');
        $oCentro = $this->crearCasaStub(20, 'BetaCentro');

        $casaRepo = $this->createMock(CasaDlRepositoryInterface::class);
        $casaRepo->method('getCasas')->willReturn([$oCasa]);

        $centroRepo = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroRepo->method('getCentros')->willReturn([$oCentro]);

        $actividadRepo = $this->createMock(ActividadRepositoryInterface::class);
        $actividadRepo->method('actividadesDeUnaCasa')->willReturn([['nom_curt' => 'A']]);

        $GLOBALS['container'] = $this->containerFromMap([
            CasaDlRepositoryInterface::class => $casaRepo,
            CentroEllasRepositoryInterface::class => $centroRepo,
            ActividadRepositoryInterface::class => $actividadRepo,
        ]);

        [, $aActividades] = ActividadesPorCasasService::actividadesPorCasas(
            6,
            $this->fecha('2030/1/1'),
            $this->fecha('2030/3/31'),
            0,
            $this->fecha('2030/3/31'),
            $this->fecha('2030/1/1'),
        );

        $this->assertArrayHasKey('AlfaCasa', $aActividades);
        $this->assertArrayHasKey('BetaCentro', $aActividades);
    }

    public function test_qcdc_sel_9_lee_ids_del_parametro_aIdCdc(): void
    {
        $capturado = null;
        $casaRepo = $this->createMock(CasaDlRepositoryInterface::class);
        $casaRepo->method('getCasas')
            ->willReturnCallback(function (array $where, array $op) use (&$capturado) {
                $capturado = ['where' => $where, 'op' => $op];
                return [];
            });

        $actividadRepo = $this->createMock(ActividadRepositoryInterface::class);

        $GLOBALS['container'] = $this->containerFromMap([
            CasaDlRepositoryInterface::class => $casaRepo,
            ActividadRepositoryInterface::class => $actividadRepo,
        ]);

        [$sCdc, ] = ActividadesPorCasasService::actividadesPorCasas(
            9,
            $this->fecha('2030/1/1'),
            $this->fecha('2030/3/31'),
            0,
            $this->fecha('2030/3/31'),
            $this->fecha('2030/1/1'),
            ['11', '22', '33'],
        );

        $this->assertSame('11,22,33', $sCdc);
        $this->assertSame('11,22,33', $capturado['where']['id_ubi']);
        $this->assertSame('IN', $capturado['op']['id_ubi']);
    }

    public function test_qcdc_sel_9_sin_ids_no_anade_clausula_id_ubi(): void
    {
        $_POST = [];
        $capturado = null;
        $casaRepo = $this->createMock(CasaDlRepositoryInterface::class);
        $casaRepo->method('getCasas')
            ->willReturnCallback(function (array $where, array $op) use (&$capturado) {
                $capturado = ['where' => $where, 'op' => $op];
                return [];
            });

        $actividadRepo = $this->createMock(ActividadRepositoryInterface::class);

        $GLOBALS['container'] = $this->containerFromMap([
            CasaDlRepositoryInterface::class => $casaRepo,
            ActividadRepositoryInterface::class => $actividadRepo,
        ]);

        [$sCdc, ] = ActividadesPorCasasService::actividadesPorCasas(
            9,
            $this->fecha('2030/1/1'),
            $this->fecha('2030/3/31'),
            0,
            $this->fecha('2030/3/31'),
            $this->fecha('2030/1/1'),
            [],
        );

        $this->assertSame('', $sCdc);
        $this->assertArrayNotHasKey('id_ubi', $capturado['where']);
    }

    public function test_casa_con_array_vacio_se_incluye_con_lista_vacia(): void
    {
        // El contrato del repo devuelve siempre `array`, por lo que la rama
        // `$a_cdc !== false` cubre tambien el caso "sin actividades" (la
        // rama elseif es defensiva y no se activa en la practica).
        $oCasa = $this->crearCasaStub(10, 'CasaSinActiv');

        $casaRepo = $this->createMock(CasaDlRepositoryInterface::class);
        $casaRepo->method('getCasas')->willReturn([$oCasa]);

        $actividadRepo = $this->createMock(ActividadRepositoryInterface::class);
        $actividadRepo->method('actividadesDeUnaCasa')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap([
            CasaDlRepositoryInterface::class => $casaRepo,
            ActividadRepositoryInterface::class => $actividadRepo,
        ]);

        [, $aActividades] = ActividadesPorCasasService::actividadesPorCasas(
            1,
            $this->fecha('2030/1/1'),
            $this->fecha('2030/3/31'),
            0,
            $this->fecha('2030/3/31'),
            $this->fecha('2030/1/1'),
        );

        $this->assertArrayHasKey('CasaSinActiv', $aActividades);
        $this->assertSame([
            'u#10#CasaSinActiv' => [],
        ], $aActividades['CasaSinActiv']);
    }

    public function test_casa_con_actividades_se_agrupa_por_nombre_ubi(): void
    {
        $oCasaA = $this->crearCasaStub(1, 'Zeta');
        $oCasaB = $this->crearCasaStub(2, 'Alfa');

        $casaRepo = $this->createMock(CasaDlRepositoryInterface::class);
        $casaRepo->method('getCasas')->willReturn([$oCasaA, $oCasaB]);

        $actividadRepo = $this->createMock(ActividadRepositoryInterface::class);
        $actividadRepo->method('actividadesDeUnaCasa')
            ->willReturnCallback(function (int $id_ubi) {
                return [['id_ubi' => $id_ubi, 'dummy' => 'x']];
            });

        $GLOBALS['container'] = $this->containerFromMap([
            CasaDlRepositoryInterface::class => $casaRepo,
            ActividadRepositoryInterface::class => $actividadRepo,
        ]);

        [, $aActividades] = ActividadesPorCasasService::actividadesPorCasas(
            1,
            $this->fecha('2030/1/1'),
            $this->fecha('2030/3/31'),
            0,
            $this->fecha('2030/3/31'),
            $this->fecha('2030/1/1'),
        );

        // ksort alfabetico => Alfa antes que Zeta.
        $claves = array_values(array_filter(array_keys($aActividades), fn ($k) => !is_int($k)));
        $this->assertSame(['Alfa', 'Zeta'], $claves);

        $this->assertArrayHasKey('u#2#Alfa', $aActividades['Alfa']);
        $this->assertArrayHasKey('u#1#Zeta', $aActividades['Zeta']);
    }

    private function crearCasaStub(int $id, string $nombre): Casa
    {
        $stub = $this->createStub(Casa::class);
        $stub->method('getId_ubi')->willReturn($id);
        $stub->method('getNombre_ubi')->willReturn($nombre);
        return $stub;
    }

    private function fecha(string $iso): DateTimeLocal
    {
        return DateTimeLocal::createFromFormat('Y/m/d', $iso);
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
