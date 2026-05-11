<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\DelegacionesRegionStgrData;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

final class DelegacionesRegionStgrDataTest extends TestCase
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

    public function test_delega_al_repositorio(): void
    {
        $repo = $this->createMock(DelegacionRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayDlRegionStgr')
            ->with(['reg1'])
            ->willReturn([10 => 'DL A', 11 => 'DL B']);

        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $repo,
        ]);

        $out = DelegacionesRegionStgrData::execute('reg1');

        $this->assertSame([10 => 'DL A', 11 => 'DL B'], $out['a_delegaciones']);
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
