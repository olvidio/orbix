<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\asistentes\application\ListaPlazasConjuntoActividades;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use frontend\shared\web\Lista;

/**
 * {@see ListaPlazasConjuntoActividades}: el listado debe tolerar una actividad con
 * `id_ubi` válido pero sin fila casa (findById null), típico de datos inconsistentes.
 */
final class ListaPlazasConjuntoActividadesTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_getlista_no_fatal_cuando_find_casa_es_null(): void
    {
        // 163001: sv + sss+ + cv; evita el bloque cargos/asistentes (in_array con $aIdCargos no definido).
        $idTipoParaSssCv = '163001';
        $miDele = 'testdl';

        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getId_activ')->willReturn(9001);
        $actividad->method('getNom_activ')->willReturn('Actividad de prueba');
        $actividad->method('getObserv')->willReturn(null);
        $actividad->method('getDl_org')->willReturn($miDele);
        $actividad->method('getId_ubi')->willReturn(555);
        // Sin plazas en la actividad: antes se leía casa; si casa es null debe seguir igual.
        $actividad->method('getPlazas')->willReturn(null);
        $actividad->method('isPublicado')->willReturn(false);

        $actividadRepo = $this->createMock(ActividadRepositoryInterface::class);
        $actividadRepo->method('getActividades')->willReturn([$actividad]);

        $resumenSvc = $this->createMock(ResumenPlazasService::class);
        $resumenSvc->expects($this->atLeastOnce())->method('setId_activ')->with(9001);
        $emptyResumenDl = [
            'calendario' => 0,
            'total_cedidas' => 0,
            'total_conseguidas' => 0,
            'total_disponibles' => 0,
            'total_ocupadas' => 0,
            'cedidas' => [],
            'conseguidas' => [],
        ];
        $resumenSvc->method('getResumen')->willReturn([$miDele => $emptyResumenDl]);

        $asistenteRepo = $this->createMock(AsistenteRepositoryInterface::class);

        $centroEncargadoRepo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $casaRepo = $this->createMock(CasaRepositoryInterface::class);
        $casaRepo->expects($this->once())->method('findById')->with(555)->willReturn(null);

        $asistenteActividadSvc = $this->createMock(AsistenteActividadService::class);

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadRepositoryInterface::class => $actividadRepo,
            ResumenPlazasService::class => $resumenSvc,
            AsistenteRepositoryInterface::class => $asistenteRepo,
            CentroEncargadoRepositoryInterface::class => $centroEncargadoRepo,
            CasaRepositoryInterface::class => $casaRepo,
            AsistenteActividadService::class => $asistenteActividadSvc,
        ]);

        $lista = new ListaPlazasConjuntoActividades();
        $lista->setMi_dele($miDele);
        $lista->setWhere([]);
        $lista->setOperador([]);
        $lista->setId_tipo_activ($idTipoParaSssCv);

        $oLista = $lista->getLista();
        $this->assertInstanceOf(Lista::class, $oLista);
        $ref = new \ReflectionProperty(Lista::class, 'aGrupos');
        $grupos = $ref->getValue($oLista);
        $this->assertArrayHasKey(9001, $grupos);
    }

    /** @param array<class-string, object> $services */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
            public function __construct(private readonly array $services)
            {
            }

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
