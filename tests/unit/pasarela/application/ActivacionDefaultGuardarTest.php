<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\pasarela\application\ActivacionDefaultGuardar;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\entity\PasarelaConfig;

final class ActivacionDefaultGuardarTest extends TestCase
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

    public function test_valor_vacio(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            PasarelaConfigRepositoryInterface::class => $this->createMock(PasarelaConfigRepositoryInterface::class),
        ]);

        $this->assertNotSame('', ActivacionDefaultGuardar::execute(''));
    }

    public function test_guarda_default(): void
    {
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->once())->method('Guardar')->with($this->callback(function (PasarelaConfig $c): bool {
            $json = $c->getJson_valor(returnArray: true);
            $this->assertIsArray($json);
            $this->assertSame('5 días', $json['default'] ?? null);

            return true;
        }))->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            PasarelaConfigRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', ActivacionDefaultGuardar::execute('5 días'));
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
