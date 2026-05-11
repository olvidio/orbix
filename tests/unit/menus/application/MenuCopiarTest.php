<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\MenuCopiar;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;

final class MenuCopiarTest extends TestCase
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

        $this->assertNotSame('', (new MenuCopiar())(1, '3'));
    }

    public function test_falla_guardar(): void
    {
        $orig = new MenuDb();
        $orig->setId_menu(10);
        $orig->setOk(true);
        $orig->setOrden(null);
        $orig->setId_grupmenu(1);

        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->willReturn($orig);
        $repo->method('getNewId')->willReturn(201);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('save-fail');

        $GLOBALS['container'] = $this->containerFromMap([
            MenuDbRepositoryInterface::class => $repo,
        ]);

        $msg = (new MenuCopiar())(10, '2');
        $this->assertNotSame('', $msg);
        $this->assertStringContainsString('save-fail', $msg);
    }

    public function test_exito_clona_y_asigna_grupo(): void
    {
        $orig = new MenuDb();
        $orig->setId_menu(10);
        $orig->setOk(true);
        $orig->setOrden([3, 1]);
        $orig->setId_grupmenu(1);
        $orig->setMenu('nom');
        $orig->setParametros('p=1');
        $orig->setId_metamenu(5);
        $orig->setMenu_perm(8);

        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->with(10)->willReturn($orig);
        $repo->method('getNewId')->willReturn(200);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (MenuDb $nuevo) {
            $this->assertSame(200, $nuevo->getId_menu());
            $this->assertTrue($nuevo->isOk() ?? false);
            $this->assertSame([3, 1], $nuevo->getOrden());
            $this->assertSame('nom', $nuevo->getMenu());
            $this->assertSame('p=1', $nuevo->getParametros());
            $this->assertSame(5, $nuevo->getId_metamenu());
            $this->assertSame(8, $nuevo->getMenu_perm());
            $this->assertSame(77, $nuevo->getId_grupmenu());
            return true;
        });

        $GLOBALS['container'] = $this->containerFromMap([
            MenuDbRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', (new MenuCopiar())(10, '77'));
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
