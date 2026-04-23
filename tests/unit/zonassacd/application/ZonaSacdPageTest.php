<?php

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\zonassacd\application\ZonaSacdPage;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Unitarios para {@see ZonaSacdPage::getData()}.
 *
 * Espejo funcional de {@see ZonaCtrPageTest}: mismo contrato resuelto
 * por contenedor, mismo calculo de `perm_des`.
 */
final class ZonaSacdPageTest extends TestCase
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
            ->willReturn([10 => 'Zona A']);

        $_SESSION['oPerm'] = $this->oPermStub([]);
        $GLOBALS['container'] = $this->containerFromMap([
            ZonaRepositoryInterface::class => $zonaRepo,
        ]);

        $this->assertSame([
            'a_opciones' => [10 => 'Zona A'],
            'perm_des' => false,
        ], ZonaSacdPage::getData());
    }

    public function test_perm_des_true_con_des_o_vcsd(): void
    {
        $zonaRepo = $this->createStub(ZonaRepositoryInterface::class);
        $zonaRepo->method('getArrayZonas')->willReturn([]);

        $_SESSION['oPerm'] = $this->oPermStub(['vcsd' => true]);
        $GLOBALS['container'] = $this->containerFromMap([
            ZonaRepositoryInterface::class => $zonaRepo,
        ]);

        $this->assertTrue(ZonaSacdPage::getData()['perm_des']);
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
