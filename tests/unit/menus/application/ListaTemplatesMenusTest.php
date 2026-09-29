<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\ListaTemplatesMenus;
use src\menus\domain\contracts\TemplateMenuRepositoryInterface;
use src\shared\security\HashB;

final class ListaTemplatesMenusTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        session_id('test-session-lista-templates');
    }

    public function test_devuelve_opciones(): void
    {
        $repo = $this->createMock(TemplateMenuRepositoryInterface::class);
        $repo->method('getArrayTemplates')->willReturn([3 => 'Tpl']);

        $out = (new ListaTemplatesMenus($repo))();
        $this->assertSame([3 => 'Tpl'], $out['a_opciones']);
        $this->assertSame([], HashB::open($out['ctx_importar'], 'menus_importar'));
    }
}
