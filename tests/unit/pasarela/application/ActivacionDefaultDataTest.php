<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\pasarela\application\ActivacionDefaultData;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\entity\PasarelaConfig;

final class ActivacionDefaultDataTest extends TestCase
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

    public function test_sin_fila_usa_valores_predeterminados_de_dominio(): void
    {
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $repo->method('findById')->with('fecha_activacion')->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            PasarelaConfigRepositoryInterface::class => $repo,
        ]);

        $out = ActivacionDefaultData::execute();
        $this->assertSame('3 días', $out['default']);
    }

    public function test_lee_default_desde_json(): void
    {
        $stored = new \stdClass();
        $stored->default = '10 días';
        $stored->excepciones = new \stdClass();

        $cfg = $this->createMock(PasarelaConfig::class);
        $cfg->method('getJson_valor')->willReturn($stored);

        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $repo->method('findById')->willReturn($cfg);

        $GLOBALS['container'] = $this->containerFromMap([
            PasarelaConfigRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('10 días', ActivacionDefaultData::execute()['default']);
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
