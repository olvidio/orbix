<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\GrupMenuColeccionUseCase;
use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\GrupMenu;
use src\menus\domain\entity\GrupMenuRole;
use src\menus\domain\entity\MenuDb;

final class GrupMenuColeccionUseCaseTest extends TestCase
{
    private mixed $previousContainer;

    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], ['id_role' => 0]);
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

    public function test_sin_id_role_devuelve_vacio(): void
    {
        $_SESSION['session_auth']['id_role'] = 0;

        $GLOBALS['container'] = $this->containerFromMap([
            GrupMenuRoleRepositoryInterface::class => $this->createMock(GrupMenuRoleRepositoryInterface::class),
            GrupMenuRepositoryInterface::class => $this->createMock(GrupMenuRepositoryInterface::class),
            MenuDbRepositoryInterface::class => $this->createMock(MenuDbRepositoryInterface::class),
        ]);

        $this->assertSame([], (new GrupMenuColeccionUseCase())());
    }

    public function test_getGrupMenuRoles_false_devuelve_vacio(): void
    {
        $_SESSION['session_auth']['id_role'] = 5;

        $gmRoleRepo = $this->createMock(GrupMenuRoleRepositoryInterface::class);
        $gmRoleRepo->method('getGrupMenuRoles')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            GrupMenuRoleRepositoryInterface::class => $gmRoleRepo,
            GrupMenuRepositoryInterface::class => $this->createMock(GrupMenuRepositoryInterface::class),
            MenuDbRepositoryInterface::class => $this->createMock(MenuDbRepositoryInterface::class),
        ]);

        $this->assertSame([], (new GrupMenuColeccionUseCase())());
    }

    public function test_ordena_por_orden_y_filtra_sin_items(): void
    {
        $_SESSION['session_auth']['id_role'] = 1;

        $r1 = new GrupMenuRole();
        $r1->setId_grupmenu(1);
        $r2 = new GrupMenuRole();
        $r2->setId_grupmenu(2);

        $stubMenu = new MenuDb();
        $stubMenu->setId_menu(1);

        $gmRoleRepo = $this->createMock(GrupMenuRoleRepositoryInterface::class);
        $gmRoleRepo->method('getGrupMenuRoles')->willReturn([$r1, $r2]);

        $menuRepo = $this->createMock(MenuDbRepositoryInterface::class);
        $menuRepo->method('getMenuDbs')->willReturnMap([
            [['id_grupmenu' => 1], [$stubMenu]],
            [['id_grupmenu' => 2], []],
        ]);

        $gA = new GrupMenu();
        $gA->setId_grupmenu(1);
        $gA->setGrup_menu('A');
        $gA->setOrden(20);

        $gmRepo = $this->createMock(GrupMenuRepositoryInterface::class);
        $gmRepo->method('findById')->willReturnMap([
            [1, $gA],
            [2, null],
        ]);

        $GLOBALS['container'] = $this->containerFromMap([
            GrupMenuRoleRepositoryInterface::class => $gmRoleRepo,
            GrupMenuRepositoryInterface::class => $gmRepo,
            MenuDbRepositoryInterface::class => $menuRepo,
        ]);

        $out = (new GrupMenuColeccionUseCase())();
        $this->assertCount(1, $out);
        $this->assertSame(1, $out[0]->getId_grupmenu());
    }

    public function test_dos_grupos_ordenados(): void
    {
        $_SESSION['session_auth']['id_role'] = 1;

        $r1 = new GrupMenuRole();
        $r1->setId_grupmenu(1);
        $r2 = new GrupMenuRole();
        $r2->setId_grupmenu(2);

        $m = new MenuDb();
        $m->setId_menu(1);

        $gmRoleRepo = $this->createMock(GrupMenuRoleRepositoryInterface::class);
        $gmRoleRepo->method('getGrupMenuRoles')->willReturn([$r1, $r2]);

        $menuRepo = $this->createMock(MenuDbRepositoryInterface::class);
        $menuRepo->method('getMenuDbs')->willReturn([$m]);

        $gLate = new GrupMenu();
        $gLate->setId_grupmenu(1);
        $gLate->setGrup_menu('Late');
        $gLate->setOrden(10);

        $gEarly = new GrupMenu();
        $gEarly->setId_grupmenu(2);
        $gEarly->setGrup_menu('Early');
        $gEarly->setOrden(3);

        $gmRepo = $this->createMock(GrupMenuRepositoryInterface::class);
        $gmRepo->method('findById')->willReturnMap([
            [1, $gLate],
            [2, $gEarly],
        ]);

        $GLOBALS['container'] = $this->containerFromMap([
            GrupMenuRoleRepositoryInterface::class => $gmRoleRepo,
            GrupMenuRepositoryInterface::class => $gmRepo,
            MenuDbRepositoryInterface::class => $menuRepo,
        ]);

        $out = (new GrupMenuColeccionUseCase())();
        $this->assertCount(2, $out);
        $this->assertSame(2, $out[0]->getId_grupmenu());
        $this->assertSame(1, $out[1]->getId_grupmenu());
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
