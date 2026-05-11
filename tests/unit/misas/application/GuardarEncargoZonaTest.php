<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\misas\application\GuardarEncargoZona;

final class GuardarEncargoZonaTest extends TestCase
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

    public function test_crea_nuevo_cuando_id_enc_cero(): void
    {
        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(501);
        $repo->expects($this->once())->method('Guardar')->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoRepositoryInterface::class => $repo,
        ]);

        $out = GuardarEncargoZona::execute([
            'id_enc' => 0,
            'id_tipo_enc' => 2,
            'id_ubi' => 0,
            'id_zona' => 10,
            'encargo' => 'Misa',
        ]);

        $this->assertSame('', $out['error']);
        $this->assertSame(501, $out['data']['id_enc']);
        $this->assertSame('', $out['data']['lugar']);
    }

    public function test_no_encuentra_encargo_existente(): void
    {
        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->with(77)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoRepositoryInterface::class => $repo,
        ]);

        $out = GuardarEncargoZona::execute(['id_enc' => 77]);
        $this->assertNotSame('', $out['error']);
        $this->assertSame(77, $out['data']['id_enc']);
    }

    public function test_falla_guardar(): void
    {
        $enc = $this->createMock(Encargo::class);
        $enc->method('getId_enc')->willReturn(3);

        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->willReturn($enc);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('g-fail');

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoRepositoryInterface::class => $repo,
        ]);

        $out = GuardarEncargoZona::execute(['id_enc' => 3, 'id_ubi' => 0]);
        $this->assertSame('g-fail', $out['error']);
        $this->assertSame(3, $out['data']['id_enc']);
    }

    public function test_exito_actualiza(): void
    {
        $enc = $this->createMock(Encargo::class);
        $enc->method('getId_enc')->willReturn(8);

        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->method('findById')->willReturn($enc);
        $repo->expects($this->once())->method('Guardar')->with($enc)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            EncargoRepositoryInterface::class => $repo,
        ]);

        $out = GuardarEncargoZona::execute([
            'id_enc' => 8,
            'id_tipo_enc' => 1,
            'id_ubi' => 0,
            'orden' => 2,
            'prioridad' => 1,
            'id_zona' => 4,
            'descripcion_lugar' => 'x',
            'encargo' => 'e',
            'idioma_enc' => 'es',
            'observ' => 'o',
        ]);

        $this->assertSame('', $out['error']);
        $this->assertSame(8, $out['data']['id_enc']);
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
