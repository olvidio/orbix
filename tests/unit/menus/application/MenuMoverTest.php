<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\MenuMover;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;

final class MenuMoverTest extends TestCase
{
    public function test_menu_no_encontrado(): void
    {
        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $this->assertNotSame('', (new MenuMover($repo))(1, '5'));
    }

    public function test_falla_guardar(): void
    {
        $menu = new MenuDb();
        $menu->setId_menu(1);
        $menu->setId_grupmenu(1);

        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->willReturn($menu);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');

        $msg = (new MenuMover($repo))(1, '99');
        $this->assertNotSame('', $msg);
        $this->assertStringContainsString('db', $msg);
    }

    public function test_exito_actualiza_grupo(): void
    {
        $menu = new MenuDb();
        $menu->setId_menu(7);
        $menu->setId_grupmenu(1);

        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->with(7)->willReturn($menu);
        $repo->method('Guardar')->with($menu)->willReturn(true);

        $this->assertSame('', (new MenuMover($repo))(7, '42'));
        $this->assertSame(42, $menu->getId_grupmenu());
    }
}
