<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\pasarela\application\NombreLista;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;

final class NombreListaTest extends TestCase
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

    public function test_estructura_con_valores_por_defecto(): void
    {
        $pasRepo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $pasRepo->method('findById')->willReturn(null);

        $tipoRepo = $this->createStub(TipoDeActividadRepositoryInterface::class);
        $tipoRepo->method('getNom_tipoPosibles')->willReturn(['tipo_nom' => [], 'nom_tipo' => []]);

        $GLOBALS['container'] = $this->containerFromMap([
            PasarelaConfigRepositoryInterface::class => $pasRepo,
            TipoDeActividadRepositoryInterface::class => $tipoRepo,
        ]);

        $out = NombreLista::execute();
        $this->assertCount(2, $out['excepciones']);
        $ids = array_column($out['excepciones'], 'id_tipo_activ');
        sort($ids);
        $this->assertSame(['111000', '111001'], $ids);
        $vals = array_column($out['excepciones'], 'valor');
        sort($vals);
        $this->assertSame(['prova1', 'prova2'], $vals);
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
