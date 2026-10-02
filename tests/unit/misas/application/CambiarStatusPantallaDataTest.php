<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\misas\application\CambiarStatusPantallaData;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\misas\application\support\IdNomJefeResolver;
use src\shared\security\HashB;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

final class CambiarStatusPantallaDataTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            $_SESSION['session_auth'] = [];
        }
        $_SESSION['session_auth']['id_usuario'] = 1;
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_emite_una_capsula_nuevo_status_por_zona_permitida(): void
    {
        $yo = $this->createMock(Usuario::class);
        $yo->method('getId_role')->willReturn(2);

        $usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepository->method('findById')->with(1)->willReturn($yo);

        $roleRepository = $this->createMock(RoleRepositoryInterface::class);
        $roleRepository->method('getArrayRoles')->willReturn([2 => 'admin']);

        $zonaRepository = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepository->method('getArrayZonas')->willReturn([9 => 'Zona nord', 12 => 'Zona sud']);

        $useCase = new CambiarStatusPantallaData(
            $zonaRepository,
            new IdNomJefeResolver($usuarioRepository, $roleRepository),
        );

        $out = $useCase->getData();

        $this->assertSame([9 => 'Zona nord', 12 => 'Zona sud'], $out['zonas_opciones']);
        $this->assertSame(EncargoDiaStatus::getArrayStatus(), $out['estados_opciones']);
        $this->assertSame(
            ['id_zona' => 9],
            HashB::open($out['zona_ctx_map']['9'], 'nuevo_status')
        );
        $this->assertSame(
            ['id_zona' => 12],
            HashB::open($out['zona_ctx_map']['12'], 'nuevo_status')
        );
    }
}
