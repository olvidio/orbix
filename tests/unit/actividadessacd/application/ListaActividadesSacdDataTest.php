<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadessacd\application\ListaActividadesSacdData;

/**
 * Con coleccion de actividades vacia no se llega a resolver personas ni
 * fases; valida periodo + forma de la respuesta.
 */
final class ListaActividadesSacdDataTest extends TestCase
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

    public function test_sin_actividades_filas_vacias_y_claves_esperadas(): void
    {
        $actRepo = $this->createMock(ActividadDlRepositoryInterface::class);
        $actRepo->method('getActividades')->willReturn([]);

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->with('sacd')->willReturn([1 => 'a']);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $centroRepo = $this->createMock(CentroEncargadoRepositoryInterface::class);

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadDlRepositoryInterface::class => $actRepo,
            CargoRepositoryInterface::class => $cargoRepo,
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
            CentroEncargadoRepositoryInterface::class => $centroRepo,
        ]);

        $out = ListaActividadesSacdData::execute([
            'tipo' => 'sv',
            'year' => '2030',
            'periodo' => 'tot_any',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertSame('sv', $out['tipo']);
        $this->assertSame([], $out['filas']);
        $this->assertFalse($out['perm_des']);
        $this->assertFalse($out['mostrar_nota_falta_sacd']);
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
