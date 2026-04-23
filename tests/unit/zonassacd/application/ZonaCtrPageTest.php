<?php

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\zonassacd\application\ZonaCtrPage;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Unitarios para {@see ZonaCtrPage::getData()}.
 *
 * El servicio resuelve el repo via `$GLOBALS['container']->get(...)` y
 * llama a `getArrayZonas()`; este test fija el nombre real del metodo
 * del contrato y lo detecta si se vuelve a renombrar o a tipar mal.
 *
 * Tambien verifica el calculo de `perm_des` segun los permisos `des` y
 * `vcsd` en `$_SESSION['oPerm']`.
 */
final class ZonaCtrPageTest extends TestCase
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

    public function test_devuelve_opciones_y_perm_des_false_sin_permisos(): void
    {
        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->expects($this->once())
            ->method('getArrayZonas')
            ->willReturn([1 => 'Centro', 2 => 'Norte']);

        $_SESSION['oPerm'] = $this->oPermStub([]);
        $GLOBALS['container'] = $this->containerFromMap([
            ZonaRepositoryInterface::class => $zonaRepo,
        ]);

        $this->assertSame([
            'a_opciones' => [1 => 'Centro', 2 => 'Norte'],
            'perm_des' => false,
        ], ZonaCtrPage::getData());
    }

    public function test_perm_des_true_si_tiene_permiso_des(): void
    {
        $zonaRepo = $this->createStub(ZonaRepositoryInterface::class);
        $zonaRepo->method('getArrayZonas')->willReturn([]);

        $_SESSION['oPerm'] = $this->oPermStub(['des' => true]);
        $GLOBALS['container'] = $this->containerFromMap([
            ZonaRepositoryInterface::class => $zonaRepo,
        ]);

        $this->assertTrue(ZonaCtrPage::getData()['perm_des']);
    }

    public function test_perm_des_true_si_tiene_permiso_vcsd(): void
    {
        $zonaRepo = $this->createStub(ZonaRepositoryInterface::class);
        $zonaRepo->method('getArrayZonas')->willReturn([]);

        $_SESSION['oPerm'] = $this->oPermStub(['vcsd' => true]);
        $GLOBALS['container'] = $this->containerFromMap([
            ZonaRepositoryInterface::class => $zonaRepo,
        ]);

        $this->assertTrue(ZonaCtrPage::getData()['perm_des']);
    }

    /**
     * @param array<string, bool> $perms
     */
    private function oPermStub(array $perms): object
    {
        return new class ($perms) {
            /** @param array<string, bool> $perms */
            public function __construct(private readonly array $perms) {}
            public function have_perm_oficina(string $p): bool
            {
                return $this->perms[$p] ?? false;
            }
        };
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
