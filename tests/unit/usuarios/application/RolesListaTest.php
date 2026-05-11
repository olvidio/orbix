<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\application;

use PHPUnit\Framework\TestCase;
use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\menus\domain\entity\GrupMenu;
use src\menus\domain\entity\GrupMenuRole;
use src\usuarios\application\rolesLista;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\entity\Usuario;

final class RolesListaTest extends TestCase
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
        $_SESSION['oConfig'] = new class {
            public function getAmbito(): string
            {
                return 'dl';
            }
        };
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

    public function test_superadmin_sv_permiso_completo_y_grup_menu(): void
    {
        $yo = $this->createMock(Usuario::class);
        $yo->method('getId_role')->willReturn(1);

        $gm = $this->createMock(GrupMenu::class);
        $gm->method('getId_grupmenu')->willReturn(7);
        $gm->method('getGrup_menu')->willReturn('Menú A');

        $role = $this->createMock(Role::class);
        $role->method('getId_role')->willReturn(3);
        $role->method('getRoleAsString')->willReturn('Editor');
        $role->method('isSf')->willReturn(true);
        $role->method('isSv')->willReturn(true);
        $role->method('getPauAsString')->willReturn('p');
        $role->method('isDmz')->willReturn(false);

        $gmr = $this->createMock(GrupMenuRole::class);
        $gmr->method('getId_grupmenu')->willReturn(7);

        $userRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $userRepo->method('findById')->with(1)->willReturn($yo);

        $grupMenuRepo = $this->createMock(GrupMenuRepositoryInterface::class);
        $grupMenuRepo->method('getGrupMenus')->willReturn([$gm]);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getRoles')->willReturn([$role]);

        $gmRoleRepo = $this->createMock(GrupMenuRoleRepositoryInterface::class);
        $gmRoleRepo->method('getGrupMenuRoles')->with(['id_role' => 3])->willReturn([$gmr]);

        $GLOBALS['container'] = $this->containerFromMap([
            UsuarioRepositoryInterface::class => $userRepo,
            GrupMenuRepositoryInterface::class => $grupMenuRepo,
            RoleRepositoryInterface::class => $roleRepo,
            GrupMenuRoleRepositoryInterface::class => $gmRoleRepo,
        ]);

        $out = rolesLista::rolesLista();

        $this->assertTrue($out['success']);
        $this->assertSame(1, $out['data']['permiso']);
        $this->assertCount(2, $out['data']['a_botones']);
        $this->assertSame('Menú A', $out['data']['a_valores'][1][6]);
        $this->assertSame('3#', $out['data']['a_valores'][1]['sel']);
    }

    public function test_sin_permiso_lista_vacia_de_botones(): void
    {
        $yo = $this->createMock(Usuario::class);
        $yo->method('getId_role')->willReturn(3);

        $gm = $this->createMock(GrupMenu::class);
        $gm->method('getId_grupmenu')->willReturn(1);
        $gm->method('getGrup_menu')->willReturn('G');

        $role = $this->createMock(Role::class);
        $role->method('getId_role')->willReturn(2);
        $role->method('getRoleAsString')->willReturn('R');
        $role->method('isSf')->willReturn(true);
        $role->method('isSv')->willReturn(true);
        $role->method('getPauAsString')->willReturn('');
        $role->method('isDmz')->willReturn(null);

        $userRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $userRepo->method('findById')->willReturn($yo);

        $grupMenuRepo = $this->createMock(GrupMenuRepositoryInterface::class);
        $grupMenuRepo->method('getGrupMenus')->willReturn([$gm]);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getRoles')->willReturn([$role]);

        $gmRoleRepo = $this->createMock(GrupMenuRoleRepositoryInterface::class);
        $gmRoleRepo->method('getGrupMenuRoles')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap([
            UsuarioRepositoryInterface::class => $userRepo,
            GrupMenuRepositoryInterface::class => $grupMenuRepo,
            RoleRepositoryInterface::class => $roleRepo,
            GrupMenuRoleRepositoryInterface::class => $gmRoleRepo,
        ]);

        $out = rolesLista::rolesLista();

        $this->assertTrue($out['success']);
        $this->assertSame(0, $out['data']['permiso']);
        $this->assertSame([], $out['data']['a_botones']);
        $this->assertArrayNotHasKey('sel', $out['data']['a_valores'][1]);
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
