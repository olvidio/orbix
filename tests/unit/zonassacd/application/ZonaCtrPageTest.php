<?php

declare(strict_types=1);

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\permisos\domain\XPermisos;
use src\zonassacd\application\ZonaCtrPage;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

final class ZonaCtrPageTest extends TestCase
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
            ->willReturn([1 => 'Centro', 2 => 'Norte']);

        $_SESSION['oPerm'] = $this->oPermStub([]);
        $page = new ZonaCtrPage($zonaRepo);

        $this->assertSame([
            'a_opciones' => [1 => 'Centro', 2 => 'Norte'],
            'perm_des' => false,
        ], $page->getData());
    }

    public function test_perm_des_true_si_tiene_permiso_des(): void
    {
        $zonaRepo = $this->createStub(ZonaRepositoryInterface::class);
        $zonaRepo->method('getArrayZonas')->willReturn([]);

        $_SESSION['oPerm'] = $this->oPermStub(['des' => true]);
        $page = new ZonaCtrPage($zonaRepo);

        $this->assertTrue($page->getData()['perm_des']);
    }

    public function test_perm_des_true_si_tiene_permiso_vcsd(): void
    {
        $zonaRepo = $this->createStub(ZonaRepositoryInterface::class);
        $zonaRepo->method('getArrayZonas')->willReturn([]);

        $_SESSION['oPerm'] = $this->oPermStub(['vcsd' => true]);
        $page = new ZonaCtrPage($zonaRepo);

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
