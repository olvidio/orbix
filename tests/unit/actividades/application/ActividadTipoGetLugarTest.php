<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadTipoGetLugar;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;

final class ActividadTipoGetLugarTest extends TestCase
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

    public function test_payload_usa_actividad_lugar(): void
    {
        $casaRepo = $this->createMock(CasaRepositoryInterface::class);
        $casaRepo->method('getArrayCasas')->willReturn([1 => 'X']);

        $centroRepo = $this->createMock(CentroRepositoryInterface::class);
        $centroRepo->method('getArrayCentrosCdc')->willReturn([]);

        $GLOBALS['container'] = new class($casaRepo, $centroRepo) {
            public function __construct(
                private readonly object $casaRepo,
                private readonly object $centroRepo
            ) {}

            public function get(string $id): object
            {
                return match ($id) {
                    CasaRepositoryInterface::class => $this->casaRepo,
                    CentroRepositoryInterface::class => $this->centroRepo,
                    default => throw new \RuntimeException($id),
                };
            }
        };

        $out = (new ActividadTipoGetLugar())->execute([
            'entrada' => 'dl|dlb',
            'isfsv' => 0,
            'ssfsv' => 'sv',
            'opcion_sel' => '1',
        ]);

        $this->assertSame('id_ubi', $out['id']);
        $this->assertTrue($out['blanco']);
        $this->assertSame('1', $out['selected']);
        $this->assertArrayHasKey(1, $out['opciones']);
    }
}
