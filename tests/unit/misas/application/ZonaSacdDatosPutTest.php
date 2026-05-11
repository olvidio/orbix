<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\misas\application\ZonaSacdDatosPut;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\ZonaSacd;

final class ZonaSacdDatosPutTest extends TestCase
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

    public function test_no_existe_fila(): void
    {
        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->method('getZonasSacds')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $repo,
        ]);

        $out = ZonaSacdDatosPut::execute(1, 2, []);
        $this->assertNotSame('', $out['error']);
    }

    public function test_falla_guardar(): void
    {
        $zs = $this->createMock(ZonaSacd::class);
        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->method('getZonasSacds')->willReturn([$zs]);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('put-fail');

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('put-fail', ZonaSacdDatosPut::execute(1, 2, ['dw1' => true])['error']);
    }

    public function test_exito_actualiza_dw(): void
    {
        $zs = $this->createMock(ZonaSacd::class);
        $zs->expects($this->once())->method('setDw1')->with(true);
        $zs->expects($this->once())->method('setDw2')->with(false);

        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->method('getZonasSacds')->with(['id_zona' => 3, 'id_nom' => 4])->willReturn([$zs]);
        $repo->method('Guardar')->with($zs)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $repo,
        ]);

        $out = ZonaSacdDatosPut::execute(3, 4, ['dw1' => 'true', 'dw2' => false]);
        $this->assertSame('', $out['error']);
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
