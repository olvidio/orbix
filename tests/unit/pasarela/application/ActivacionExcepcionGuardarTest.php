<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\pasarela\application\ActivacionExcepcionGuardar;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;

final class ActivacionExcepcionGuardarTest extends TestCase
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

    public function test_falta_id_tipo_activ(): void
    {
        $this->assertNotSame('', ActivacionExcepcionGuardar::execute('', 'x'));
    }

    public function test_falta_valor(): void
    {
        $this->assertNotSame('', ActivacionExcepcionGuardar::execute('111000', ''));
    }

    public function test_guarda_excepcion(): void
    {
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->once())->method('Guardar')->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            PasarelaConfigRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', ActivacionExcepcionGuardar::execute('111000', '2 días'));
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
