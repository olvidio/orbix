<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\ListaTemplatesMenus;
use src\menus\domain\contracts\TemplateMenuRepositoryInterface;

final class ListaTemplatesMenusTest extends TestCase
{
    public function test_devuelve_opciones(): void
    {
        $repo = $this->createMock(TemplateMenuRepositoryInterface::class);
        $repo->method('getArrayTemplates')->willReturn([3 => 'Tpl']);

        $this->assertSame(['a_opciones' => [3 => 'Tpl']], (new ListaTemplatesMenus($repo))());
    }
}
