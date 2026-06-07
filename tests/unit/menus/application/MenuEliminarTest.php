<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\MenuEliminar;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;

final class MenuEliminarTest extends TestCase
{
    public function test_menu_no_encontrado(): void
    {
        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->with(3)->willReturn(null);

        $this->assertNotSame('', (new MenuEliminar($repo))(3));
    }

    public function test_falla_eliminar(): void
    {
        $menu = $this->createMock(MenuDb::class);

        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->willReturn($menu);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('err');

        $msg = (new MenuEliminar($repo))(1);
        $this->assertNotSame('', $msg);
        $this->assertStringContainsString('err', $msg);
    }

    public function test_exito(): void
    {
        $menu = $this->createMock(MenuDb::class);

        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->willReturn($menu);
        $repo->method('Eliminar')->with($menu)->willReturn(true);

        $this->assertSame('', (new MenuEliminar($repo))(9));
    }
}
