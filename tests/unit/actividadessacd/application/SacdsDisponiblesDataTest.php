<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadessacd\application\SacdsDisponiblesData;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;

/**
 * Sin `encargossacd` instalado en sesion solo se rellena `sacds_todos`
 * via {@see PersonaSacdRepositoryInterface::getSacdsBySelect}.
 */
final class SacdsDisponiblesDataTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
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

    public function test_sacds_todos_desde_repositorio_sacds_ctr_vacio_sin_encargossacd(): void {
        $p1 = $this->createMock(PersonaSacd::class);
        $p1->method('getId_nom')->willReturn(10);
        $p1->method('getPrefApellidosNombre')->willReturn('Uno, Persona');

        $repo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $repo->method('getSacdsBySelect')->with(15)->willReturn([$p1]);

        $out = (new \src\actividadessacd\application\SacdsDisponiblesData($this->createMock(\src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface::class), $this->createMock(\src\encargossacd\domain\contracts\EncargoRepositoryInterface::class), $this->createMock(\src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface::class), $repo))->execute(['id_activ' => 99, 'seleccion' => 15]);
        $this->assertSame(99, $out['id_activ']);
        $this->assertSame([], $out['sacds_ctr']);
        $this->assertSame([
            ['id_nom' => 10, 'ap_nom' => 'Uno, Persona'],
        ], $out['sacds_todos']);
    }

    /**
     * @param class-string $iface
     */
    private function containerOne(string $iface, object $service): object
    {
        return new class($iface, $service) {
            public function __construct(
                private readonly string $iface,
                private readonly object $service
            ) {}

            public function get(string $id): object
            {
                if ($id !== $this->iface) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->service;
            }
        };
    }
}
