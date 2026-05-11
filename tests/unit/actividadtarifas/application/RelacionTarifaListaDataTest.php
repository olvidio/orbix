<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\RelacionTarifaListaData;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;

/**
 * Con coleccion vacia no se instancia fila con {@see TiposActividades}.
 */
final class RelacionTarifaListaDataTest extends TestCase
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

    public function test_sin_relaciones(): void
    {
        $_SESSION['oPerm'] = new class {
            public function have_perm_oficina(string $p): bool
            {
                return false;
            }
        };

        $repoRel = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repoRel->method('getTipoActivTarifas')->willReturn([]);

        $repoTipo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repoTipo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerFromMap([
            RelacionTarifaTipoActividadRepositoryInterface::class => $repoRel,
            TipoTarifaRepositoryInterface::class => $repoTipo,
        ]);

        $out = RelacionTarifaListaData::execute();
        $this->assertSame([], $out['a_valores']);
        $this->assertCount(3, $out['a_cabeceras']);
        $this->assertFalse($out['puede_anadir']);
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
