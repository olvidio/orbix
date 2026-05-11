<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\TelecoDescLista;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;

final class TelecoDescListaTest extends TestCase
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

    public function test_envuelve_opciones_del_repositorio(): void
    {
        $repo = $this->createMock(DescTelecoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayDescTelecoUbis')
            ->with(3)
            ->willReturn(['a' => 'A', 'b' => 'B']);

        $GLOBALS['container'] = $this->containerFromMap([
            DescTelecoRepositoryInterface::class => $repo,
        ]);

        $this->assertSame(['a_desc' => ['a' => 'A', 'b' => 'B']], TelecoDescLista::execute(3));
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
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
