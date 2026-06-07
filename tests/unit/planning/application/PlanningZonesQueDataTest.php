<?php

declare(strict_types=1);

namespace Tests\unit\planning\application;

use PHPUnit\Framework\TestCase;
use src\planning\application\PlanningZonesQueData;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

final class PlanningZonesQueDataTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], ['id_usuario' => 1]);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_sin_zonas_disponibles_devuelve_error(): void
    {
        $u = $this->createMock(Usuario::class);
        $u->method('getId_role')->willReturn(10);

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->with(1)->willReturn($u);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([10 => 'admin']);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('getArrayZonas')->willReturn([]);

        $useCase = new PlanningZonesQueData($usuarioRepo, $roleRepo, $zonaRepo);
        $out = $useCase->execute();
        $this->assertNotSame('', $out['error']);
        $this->assertSame([], $out['opciones_zonas']);
    }

    public function test_exito_con_opciones(): void
    {
        $u = $this->createMock(Usuario::class);
        $u->method('getId_role')->willReturn(3);

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->willReturn($u);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([3 => 'otro']);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->expects($this->once())
            ->method('getArrayZonas')
            ->with(null)
            ->willReturn([5 => 'Zona A']);

        $useCase = new PlanningZonesQueData($usuarioRepo, $roleRepo, $zonaRepo);
        $out = $useCase->execute();
        $this->assertSame('', $out['error']);
        $this->assertSame([5 => 'Zona A'], $out['opciones_zonas']);
    }

    public function test_p_sacd_jefe_calendario_no_exige_id_nom_jefe(): void
    {
        $_SESSION['oConfig'] = new class {
            public function is_jefeCalendario(): bool
            {
                return true;
            }
        };

        $u = $this->createMock(Usuario::class);
        $u->method('getId_role')->willReturn(7);

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->willReturn($u);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([7 => 'p-sacd']);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('getArrayZonas')->with(null)->willReturn([1 => 'Z']);

        $useCase = new PlanningZonesQueData($usuarioRepo, $roleRepo, $zonaRepo);
        $out = $useCase->execute();
        $this->assertSame('', $out['error']);
    }
}
