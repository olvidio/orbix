<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadessacd\application\SacdAsignar;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\entity\Asistente;

/**
 * Unitarios del use case {@see SacdAsignar}: mockea los 4 repos via contenedor
 * DI. Cubre validaciones, primer hueco libre, sin huecos disponibles, rama sv
 * (crea Asistencia) vs sf (no), y errores de Guardar en ambos caminos.
 *
 * Para la rama sv se inicializa sesion minima porque {@see SacdAsignar}
 * llama a `ConfigGlobal::mi_delef()` al crear la entidad Asistente.
 */
final class SacdAsignarTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
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

    public function test_sin_id_activ_devuelve_error(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([]);

        $out = SacdAsignar::execute(['id_activ' => 0, 'id_nom' => 123]);
        $this->assertStringContainsString('faltan parametros', $out);
    }

    public function test_sin_id_nom_devuelve_error(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([]);

        $out = SacdAsignar::execute(['id_activ' => 99, 'id_nom' => 0]);
        $this->assertStringContainsString('faltan parametros', $out);
    }

    public function test_todos_los_cargos_sacd_ocupados_devuelve_error(): void
    {
        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->with('sacd')->willReturn([
            2001 => 'sacd1',
            2002 => 'sacd2',
        ]);

        $ocupado1 = new ActividadCargo();
        $ocupado1->setId_cargo(2001);
        $ocupado2 = new ActividadCargo();
        $ocupado2->setId_cargo(2002);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([$ocupado1, $ocupado2]);
        $activCargoRepo->expects($this->never())->method('Guardar');

        $GLOBALS['container'] = $this->containerFromMap([
            CargoRepositoryInterface::class => $cargoRepo,
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
        ]);

        $out = SacdAsignar::execute(['id_activ' => 500, 'id_nom' => 111]);
        $this->assertStringContainsString('No puede haber tantos cargos de sacd', $out);
    }

    public function test_primer_hueco_libre_se_asigna_y_actividad_sf_no_crea_asistencia(): void
    {
        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->with('sacd')->willReturn([
            2001 => 'sacd1',
            2002 => 'sacd2',
        ]);

        $ocupado = new ActividadCargo();
        $ocupado->setId_cargo(2001);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([$ocupado]);
        $activCargoRepo->method('getNewId')->willReturn(987654);

        $guardado = null;
        $activCargoRepo->expects($this->once())
            ->method('Guardar')
            ->with($this->callback(function (ActividadCargo $oCargo) use (&$guardado) {
                $guardado = $oCargo;
                return true;
            }))
            ->willReturn(true);

        $oActivSf = new ActividadAll();
        $oActivSf->setId_tipo_activ(271000);
        $actividadRepo = $this->createMock(ActividadDlRepositoryInterface::class);
        $actividadRepo->method('findById')->with(500)->willReturn($oActivSf);

        // La rama sf NO debe tocar el repositorio de asistentes.
        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->expects($this->never())->method('Guardar');

        $GLOBALS['container'] = $this->containerFromMap([
            CargoRepositoryInterface::class => $cargoRepo,
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
            ActividadDlRepositoryInterface::class => $actividadRepo,
            AsistenteDlRepositoryInterface::class => $asistenteRepo,
        ]);

        $out = SacdAsignar::execute(['id_activ' => 500, 'id_nom' => 111]);
        $this->assertSame('', $out);
        $this->assertNotNull($guardado);
        $this->assertSame(2002, $guardado->getId_cargo());
        $this->assertSame(111, $guardado->getId_nom());
        $this->assertSame(500, $guardado->getId_activ());
        $this->assertSame(987654, $guardado->getId_item());
    }

    public function test_actividad_sv_crea_asistencia(): void
    {
        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([2001 => 'sacd1']);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([]);
        $activCargoRepo->method('getNewId')->willReturn(123);
        $activCargoRepo->method('Guardar')->willReturn(true);

        $oActivSv = new ActividadAll();
        $oActivSv->setId_tipo_activ(111111);
        $actividadRepo = $this->createMock(ActividadDlRepositoryInterface::class);
        $actividadRepo->method('findById')->willReturn($oActivSv);

        $asisGuardado = null;
        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->expects($this->once())
            ->method('Guardar')
            ->with($this->callback(function (Asistente $oAsis) use (&$asisGuardado) {
                $asisGuardado = $oAsis;
                return true;
            }))
            ->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            CargoRepositoryInterface::class => $cargoRepo,
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
            ActividadDlRepositoryInterface::class => $actividadRepo,
            AsistenteDlRepositoryInterface::class => $asistenteRepo,
        ]);

        $out = SacdAsignar::execute(['id_activ' => 500, 'id_nom' => 111]);
        $this->assertSame('', $out);
        $this->assertNotNull($asisGuardado);
        $this->assertSame(500, $asisGuardado->getId_activ());
        $this->assertSame(111, $asisGuardado->getId_nom());
        $this->assertFalse($asisGuardado->isPropio());
        $this->assertFalse($asisGuardado->isFalta());
    }

    public function test_error_si_guardar_cargo_falla(): void
    {
        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([2001 => 'sacd1']);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([]);
        $activCargoRepo->method('getNewId')->willReturn(1);
        $activCargoRepo->method('Guardar')->willReturn(false);

        $actividadRepo = $this->createMock(ActividadDlRepositoryInterface::class);
        $actividadRepo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerFromMap([
            CargoRepositoryInterface::class => $cargoRepo,
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
            ActividadDlRepositoryInterface::class => $actividadRepo,
        ]);

        $out = SacdAsignar::execute(['id_activ' => 500, 'id_nom' => 111]);
        $this->assertStringContainsString('no se ha guardado el cargo', $out);
    }

    public function test_error_si_guardar_asistencia_falla_en_sv(): void
    {
        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([2001 => 'sacd1']);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([]);
        $activCargoRepo->method('getNewId')->willReturn(1);
        $activCargoRepo->method('Guardar')->willReturn(true);

        $oActivSv = new ActividadAll();
        $oActivSv->setId_tipo_activ(111111);
        $actividadRepo = $this->createMock(ActividadDlRepositoryInterface::class);
        $actividadRepo->method('findById')->willReturn($oActivSv);

        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->method('Guardar')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            CargoRepositoryInterface::class => $cargoRepo,
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
            ActividadDlRepositoryInterface::class => $actividadRepo,
            AsistenteDlRepositoryInterface::class => $asistenteRepo,
        ]);

        $out = SacdAsignar::execute(['id_activ' => 500, 'id_nom' => 111]);
        $this->assertStringContainsString('no se ha guardado la asistencia', $out);
    }

    public function test_actividad_inexistente_guarda_cargo_y_no_crea_asistencia(): void
    {
        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([2001 => 'sacd1']);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([]);
        $activCargoRepo->method('getNewId')->willReturn(1);
        $activCargoRepo->method('Guardar')->willReturn(true);

        $actividadRepo = $this->createMock(ActividadDlRepositoryInterface::class);
        $actividadRepo->method('findById')->willReturn(null);

        $asistenteRepo = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteRepo->expects($this->never())->method('Guardar');

        $GLOBALS['container'] = $this->containerFromMap([
            CargoRepositoryInterface::class => $cargoRepo,
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
            ActividadDlRepositoryInterface::class => $actividadRepo,
            AsistenteDlRepositoryInterface::class => $asistenteRepo,
        ]);

        $out = SacdAsignar::execute(['id_activ' => 500, 'id_nom' => 111]);
        $this->assertSame('', $out);
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
