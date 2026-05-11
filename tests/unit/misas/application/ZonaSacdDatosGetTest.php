<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\misas\application\ZonaSacdDatosGet;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\ZonaSacd;

final class ZonaSacdDatosGetTest extends TestCase
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

    public function test_no_existe(): void
    {
        $zRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zRepo->method('getZonasSacds')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $zRepo,
        ]);

        $out = ZonaSacdDatosGet::execute(1, 2);
        $this->assertNotSame('', $out['error']);
        $this->assertSame([], $out['payload']);
    }

    public function test_payload_con_persona(): void
    {
        $zs = $this->createMock(ZonaSacd::class);
        $zs->method('isDw1')->willReturn(true);
        $zs->method('isDw2')->willReturn(false);
        $zs->method('isDw3')->willReturn(null);
        $zs->method('isDw4')->willReturn(null);
        $zs->method('isDw5')->willReturn(null);
        $zs->method('isDw6')->willReturn(null);
        $zs->method('isDw7')->willReturn(null);

        $persona = $this->createMock(PersonaSacd::class);
        $persona->method('getNombreApellidos')->willReturn('Juan Pérez');

        $zRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zRepo->method('getZonasSacds')->willReturn([$zs]);

        $pRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $pRepo->method('findById')->with(9)->willReturn($persona);

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $zRepo,
            PersonaSacdRepositoryInterface::class => $pRepo,
        ]);

        $out = ZonaSacdDatosGet::execute(5, 9);
        $this->assertSame('', $out['error']);
        $this->assertSame('Juan Pérez', $out['payload']['nombre_sacd']);
        $this->assertTrue($out['payload']['dw1']);
        $this->assertFalse($out['payload']['dw2']);
    }

    public function test_sin_persona_usa_interrogacion(): void
    {
        $zs = $this->createMock(ZonaSacd::class);
        $zs->method('isDw1')->willReturn(null);
        $zs->method('isDw2')->willReturn(null);
        $zs->method('isDw3')->willReturn(null);
        $zs->method('isDw4')->willReturn(null);
        $zs->method('isDw5')->willReturn(null);
        $zs->method('isDw6')->willReturn(null);
        $zs->method('isDw7')->willReturn(null);

        $zRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zRepo->method('getZonasSacds')->willReturn([$zs]);

        $pRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $pRepo->method('findById')->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $zRepo,
            PersonaSacdRepositoryInterface::class => $pRepo,
        ]);

        $out = ZonaSacdDatosGet::execute(1, 2);
        $this->assertSame('', $out['error']);
        $this->assertSame('?', $out['payload']['nombre_sacd']);
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
