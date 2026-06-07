<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\ListaMetaMenus;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;

final class ListaMetaMenusTest extends TestCase
{
    public function test_devuelve_opciones(): void
    {
        $repo = $this->createMock(MetaMenuRepositoryInterface::class);
        $repo->method('getArrayMetaMenus')->willReturn([7 => 'Meta']);

        $this->assertSame(['a_opciones' => [7 => 'Meta']], (new ListaMetaMenus($repo))());
    }
}
