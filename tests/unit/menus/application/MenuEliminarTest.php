<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\MenuEliminar;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;

final class MenuEliminarTest extends TestCase
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
        $repo->method('findById')->with(3)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            MenuDbRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', (new MenuEliminar())(3));
    }

    public function test_falla_eliminar(): void
    {
        $menu = $this->createMock(MenuDb::class);

        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->willReturn($menu);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('err');

        $GLOBALS['container'] = $this->containerFromMap([
            MenuDbRepositoryInterface::class => $repo,
        ]);

        $msg = (new MenuEliminar())(1);
        $this->assertNotSame('', $msg);
        $this->assertStringContainsString('err', $msg);
    }

    public function test_exito(): void
    {
        $menu = $this->createMock(MenuDb::class);

        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->willReturn($menu);
        $repo->method('Eliminar')->with($menu)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            MenuDbRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', (new MenuEliminar())(9));
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
