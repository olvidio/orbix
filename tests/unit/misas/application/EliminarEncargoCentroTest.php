<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\misas\application\EliminarEncargoCentro;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\entity\EncargoCtr;
final class EliminarEncargoCentroTest extends TestCase
{
    private const UUID = '550e8400-e29b-41d4-a716-446655440000';

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

    public function test_id_vacio(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            EncargoCtrRepositoryInterface::class => $this->createMock(EncargoCtrRepositoryInterface::class),
        ]);

        $this->assertNotSame('', EliminarEncargoCentro::execute(''));
    }

    public function test_no_encontrado(): void
    {
        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoCtrRepositoryInterface::class => $repo,
        ]);

        $msg = EliminarEncargoCentro::execute(self::UUID);
        $this->assertStringContainsString(self::UUID, $msg);
    }

    public function test_falla_eliminar(): void
    {
        $ctr = $this->createMock(EncargoCtr::class);
        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->method('findById')->willReturn($ctr);
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('del-fail');

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoCtrRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('del-fail', EliminarEncargoCentro::execute(self::UUID));
    }

    public function test_exito(): void
    {
        $ctr = $this->createMock(EncargoCtr::class);
        $repo = $this->createMock(EncargoCtrRepositoryInterface::class);
        $repo->method('findById')->willReturn($ctr);
        $repo->expects($this->once())->method('Eliminar')->with($ctr)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoCtrRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', EliminarEncargoCentro::execute(self::UUID));
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
