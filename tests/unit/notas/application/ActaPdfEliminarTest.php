<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\notas\application\ActaPdfEliminar;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Acta;

final class ActaPdfEliminarTest extends TestCase
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

    public function test_acta_vacia(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            ActaRepositoryInterface::class => $this->createMock(ActaRepositoryInterface::class),
        ]);

        $this->assertNotSame('', ActaPdfEliminar::execute([]));
    }

    public function test_acta_no_encontrada(): void
    {
        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->with('42')->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            ActaRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', ActaPdfEliminar::execute(['acta_num' => '42']));
    }

    public function test_falla_guardar(): void
    {
        $acta = $this->createMock(Acta::class);
        $acta->expects($this->once())->method('setPdf')->with('');

        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->willReturn($acta);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('pdf-del-fail');

        $GLOBALS['container'] = $this->containerFromMap([
            ActaRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('pdf-del-fail', ActaPdfEliminar::execute(['acta_num' => '1']));
    }

    public function test_exito(): void
    {
        $acta = $this->createMock(Acta::class);
        $acta->expects($this->once())->method('setPdf')->with('');

        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->willReturn($acta);
        $repo->expects($this->once())->method('Guardar')->with($acta)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            ActaRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', ActaPdfEliminar::execute(['acta_num' => '9']));
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
