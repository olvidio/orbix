<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\GrupMenuListaUseCase;
use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\entity\GrupMenu;

final class GrupMenuListaUseCaseTest extends TestCase
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

    public function test_mapea_filas(): void
    {
        $g1 = new GrupMenu();
        $g1->setId_grupmenu(10);
        $g1->setGrup_menu('G1');
        $g1->setOrden(2);

        $g2 = new GrupMenu();
        $g2->setId_grupmenu(20);
        $g2->setGrup_menu('G2');
        $g2->setOrden(1);

        $repo = $this->createMock(GrupMenuRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getGrupMenus')
            ->with(['_ordre' => 'orden'])
            ->willReturn([$g1, $g2]);

        $GLOBALS['container'] = $this->containerFromMap([
            GrupMenuRepositoryInterface::class => $repo,
        ]);

        $data = (new GrupMenuListaUseCase())();
        $this->assertSame([10 => 'G1', 20 => 'G2'], $data['a_lista']);
        $this->assertSame([
            1 => ['sel' => '10#', 1 => 'G1', 2 => 2],
            2 => ['sel' => '20#', 1 => 'G2', 2 => 1],
        ], $data['a_valores']);
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
