<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\application;

use PHPUnit\Framework\TestCase;
use src\usuarios\application\usuariosLista;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\entity\Usuario;

final class UsuariosListaTest extends TestCase
{
    private mixed $previousContainer;

    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            $_SESSION['session_auth'] = [];
        }
        $_SESSION['session_auth']['id_usuario'] = 1;
        $_SESSION['session_auth']['sfsv'] = 1;
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_rol_mayor_que_3_devuelve_error(): void
    {
        $yo = $this->createMock(Usuario::class);
        $yo->method('getId_role')->willReturn(4);

        $userRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $userRepo->method('findById')->with(1)->willReturn($yo);

        $GLOBALS['container'] = $this->containerFromMap([
            UsuarioRepositoryInterface::class => $userRepo,
            RoleRepositoryInterface::class => $this->createMock(RoleRepositoryInterface::class),
        ]);

        $out = usuariosLista::usuariosLista('');

        $this->assertFalse($out['success']);
        $this->assertSame(_('no tiene permisos para ver esto'), $out['mensaje']);
    }

    public function test_superadmin_lista_usuario_con_role_sv(): void
    {
        $yo = $this->createMock(Usuario::class);
        $yo->method('getId_role')->willReturn(1);

        $listado = $this->createMock(Usuario::class);
        $listado->method('getId_usuario')->willReturn(9);
        $listado->method('getUsuarioAsString')->willReturn('pep');
        $listado->method('getNomUsuarioAsString')->willReturn('Pep Vila');
        $listado->method('getEmailAsString')->willReturn('p@x.test');
        $listado->method('getId_role')->willReturn(2);

        $role = $this->createMock(Role::class);
        $role->method('getRoleAsString')->willReturn('Admin');
        $role->method('isSv')->willReturn(true);
        $role->method('isSf')->willReturn(false);

        $userRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $userRepo->method('findById')->with(1)->willReturn($yo);
        $userRepo->method('getUsuarios')->willReturn([$listado]);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('findById')->with(2)->willReturn($role);

        $GLOBALS['container'] = $this->containerFromMap([
            UsuarioRepositoryInterface::class => $userRepo,
            RoleRepositoryInterface::class => $roleRepo,
        ]);

        $out = usuariosLista::usuariosLista('');

        $this->assertTrue($out['success']);
        $this->assertIsArray($out['data']);
        $this->assertSame('9#', $out['data']['a_valores'][1]['sel']);
        $this->assertSame('Admin', $out['data']['a_valores'][1][3]);
        $this->assertSame('p@x.test', $out['data']['a_valores'][1][5]);
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
