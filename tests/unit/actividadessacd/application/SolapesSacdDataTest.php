<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\contracts\CargoOAsistenteInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividadessacd\application\SolapesSacdData;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;

/**
 * Con solapes vacios no se consultan actividades por id ni personas;
 * comprueba periodo + titulo + filas vacias.
 */
final class SolapesSacdDataTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
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

    public function test_sin_solapes_filas_vacias(): void
    {
        $faseRepo = $this->createMock(ActividadFaseRepositoryInterface::class);
        $faseRepo->method('findById')->willReturn(null);

        $actDlRepo = $this->createMock(ActividadDlRepositoryInterface::class);
        $actDlRepo->method('getActividades')->willReturn([]);

        $personaSacdRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personaSacdRepo->method('getPersonas')->willReturn([]);

        $cargoOAsis = $this->createMock(CargoOAsistenteInterface::class);
        $cargoOAsis->method('getSolapes')->willReturn([]);

        $actAllRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actAllRepo->expects($this->never())->method('findById');

        $tareaRepo = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $tareaRepo->expects($this->never())->method('getSacdAprobado');

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadFaseRepositoryInterface::class => $faseRepo,
            ActividadDlRepositoryInterface::class => $actDlRepo,
            PersonaSacdRepositoryInterface::class => $personaSacdRepo,
            CargoOAsistenteInterface::class => $cargoOAsis,
            ActividadAllRepositoryInterface::class => $actAllRepo,
            ActividadProcesoTareaRepositoryInterface::class => $tareaRepo,
        ]);

        $out = SolapesSacdData::execute([
            'year' => '2030',
            'periodo' => 'tot_any',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertSame([], $out['filas']);
        $this->assertSame('', $out['texto_fase_ok_sacd']);
        $this->assertIsString($out['titulo']);
        $this->assertNotSame('', $out['inicio_iso']);
        $this->assertNotSame('', $out['fin_iso']);
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
