<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\MenuMover;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;

final class MenuMoverTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_menu_no_encontrado(): void
    {
        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            MenuDbRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', (new MenuMover())(1, '5'));
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

        $GLOBALS['container'] = $this->containerFromMap([
            MenuDbRepositoryInterface::class => $repo,
        ]);

        $msg = (new MenuMover())(1, '99');
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

        $GLOBALS['container'] = $this->containerFromMap([
            MenuDbRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', (new MenuMover())(7, '42'));
        $this->assertSame(42, $menu->getId_grupmenu());
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
