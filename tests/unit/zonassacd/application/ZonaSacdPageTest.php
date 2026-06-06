<?php

declare(strict_types=1);

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\permisos\domain\XPermisos;
use src\zonassacd\application\ZonaSacdPage;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

final class ZonaSacdPageTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
    }

    protected function tearDown(): void
    {
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
        $page = new ZonaSacdPage($zonaRepo);

        $this->assertSame([
            'a_opciones' => [10 => 'Zona A'],
            'perm_des' => false,
        ], $page->getData());
    }

    public function test_perm_des_true_con_des_o_vcsd(): void
    {
        $zonaRepo = $this->createStub(ZonaRepositoryInterface::class);
        $zonaRepo->method('getArrayZonas')->willReturn([]);

        $_SESSION['oPerm'] = $this->oPermStub(['vcsd' => true]);
        $page = new ZonaSacdPage($zonaRepo);

        $this->assertTrue($page->getData()['perm_des']);
    }

    /**
     * @param array<string, bool> $perms
     */
    private function oPermStub(array $perms): XPermisos
    {
        $stub = $this->createMock(XPermisos::class);
        $stub->method('have_perm_oficina')->willReturnCallback(
            static fn (string $p): bool => $perms[$p] ?? false
        );
        return $stub;
    }
}
