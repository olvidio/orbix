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
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], ['id_role' => 0]);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_sin_id_role_devuelve_vacio(): void
    {
        $_SESSION['session_auth']['id_role'] = 0;

        $useCase = new GrupMenuColeccionUseCase(
            $this->createMock(GrupMenuRoleRepositoryInterface::class),
            $this->createMock(GrupMenuRepositoryInterface::class),
            $this->createMock(MenuDbRepositoryInterface::class),
        );

        $this->assertSame([], $useCase());
    }

    public function test_getGrupMenuRoles_vacio_devuelve_vacio(): void
    {
        $_SESSION['session_auth']['id_role'] = 5;

        $gmRoleRepo = $this->createMock(GrupMenuRoleRepositoryInterface::class);
        $gmRoleRepo->method('getGrupMenuRoles')->willReturn([]);

        $useCase = new GrupMenuColeccionUseCase(
            $gmRoleRepo,
            $this->createMock(GrupMenuRepositoryInterface::class),
            $this->createMock(MenuDbRepositoryInterface::class),
        );

        $this->assertSame([], $useCase());
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

        $useCase = new GrupMenuColeccionUseCase($gmRoleRepo, $gmRepo, $menuRepo);

        $out = $useCase();
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

        $useCase = new GrupMenuColeccionUseCase($gmRoleRepo, $gmRepo, $menuRepo);

        $out = $useCase();
        $this->assertCount(2, $out);
        $this->assertSame(2, $out[0]->getId_grupmenu());
        $this->assertSame(1, $out[1]->getId_grupmenu());
    }
}
