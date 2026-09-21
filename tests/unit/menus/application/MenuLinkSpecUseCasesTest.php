<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\MenusBurgerLayoutDataUseCase;
use src\menus\application\MenusLegacyLayoutItemsUseCase;
use src\menus\application\MenusVisiblesPorGrupoMenuUseCase;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\entity\MenuDb;
use src\menus\domain\entity\MetaMenu;

final class MenuLinkSpecUseCasesTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION = ['iPermMenus' => 1];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_legacy_layout_returns_unsigned_link_spec(): void
    {
        $useCase = new MenusLegacyLayoutItemsUseCase(
            $this->menuRepositoryWith([$this->menu()]),
            $this->metaMenuRepository()
        );

        $items = $useCase('2');

        $this->assertSame(
            ['path' => 'frontend/menus/controller/menus_que.php', 'parametros' => 'id_grupmenu=2'],
            $items[0]['link_spec'] ?? null
        );
        $this->assertArrayNotHasKey('full_url', $items[0]);
        $this->assertArrayNotHasKey('parametros', $items[0]);
    }

    public function test_burger_layout_returns_unsigned_link_specs_and_nodes(): void
    {
        $repository = $this->createMock(MenuDbRepositoryInterface::class);
        $repository->expects($this->exactly(2))
            ->method('getMenuDbs')
            ->willReturnOnConsecutiveCalls([], [$this->menu()]);
        $useCase = new MenusBurgerLayoutDataUseCase($repository, $this->metaMenuRepository());

        $data = $useCase([2 => 'Menus']);

        $node = $data['menu_config']['Menus'][0] ?? null;
        $this->assertIsArray($node);
        $this->assertSame(
            ['path' => 'frontend/menus/controller/menus_que.php', 'parametros' => 'id_grupmenu=2'],
            $node['link_spec'] ?? null
        );
        $this->assertArrayNotHasKey('onClick', $node);
        $this->assertSame([], $data['user_menu_nodes']);
    }

    public function test_group_visible_menus_return_unsigned_link_spec(): void
    {
        $useCase = new MenusVisiblesPorGrupoMenuUseCase(
            $this->menuRepositoryWith([$this->menu()]),
            $this->metaMenuRepository()
        );

        $items = $useCase(2);

        $this->assertSame(
            ['path' => 'frontend/menus/controller/menus_que.php', 'parametros' => 'id_grupmenu=2'],
            $items[0]['link_spec'] ?? null
        );
        $this->assertArrayNotHasKey('full_url', $items[0]);
        $this->assertArrayNotHasKey('parametros', $items[0]);
    }

    private function menuRepositoryWith(array $menus): MenuDbRepositoryInterface
    {
        $repository = $this->createMock(MenuDbRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('getMenuDbs')
            ->willReturn($menus);

        return $repository;
    }

    private function metaMenuRepository(): MetaMenuRepositoryInterface
    {
        $repository = $this->createMock(MetaMenuRepositoryInterface::class);
        $repository->method('findById')
            ->with(10)
            ->willReturn($this->metaMenu());

        return $repository;
    }

    private function menu(): MenuDb
    {
        $menu = new MenuDb();
        $menu->setAllAttributes([
            'id_menu' => 1,
            'orden' => [1],
            'menu' => 'Menús',
            'parametros' => 'id_grupmenu=2',
            'id_metamenu' => 10,
            'menu_perm' => 1,
            'id_grupmenu' => 2,
            'ok' => true,
        ]);

        return $menu;
    }

    private function metaMenu(): MetaMenu
    {
        $metaMenu = new MetaMenu();
        $metaMenu->setId_metamenu(10);
        $metaMenu->setId_mod(null);
        $metaMenu->setUrl('frontend/menus/controller/menus_que.php');

        return $metaMenu;
    }
}
