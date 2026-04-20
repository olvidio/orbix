<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ListaCentrosActivDatos;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

final class ListaCentrosActivDatosTest extends TestCase
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

    public function test_sin_centros_solo_estilo(): void
    {
        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->method('getCentros')->willReturn([]);

        $encRepo = $this->createMock(CentroEncargadoRepositoryInterface::class);

        $GLOBALS['container'] = new class($centroDl, $encRepo) {
            public function __construct(
                private readonly object $centroDl,
                private readonly object $encRepo
            ) {}

            public function get(string $id): object
            {
                return match ($id) {
                    CentroDlRepositoryInterface::class => $this->centroDl,
                    CentroEncargadoRepositoryInterface::class => $this->encRepo,
                    default => throw new \RuntimeException($id),
                };
            }
        };

        $out = (new ListaCentrosActivDatos())->ejecutar([
            'id_ctr_num' => 0,
            'id_ctr' => [],
            'periodo' => 'actual',
        ]);

        $this->assertArrayHasKey('html', $out);
        $this->assertStringContainsString('.responsable', $out['html']);
        $this->assertStringNotContainsString('<h3>', $out['html']);
    }
}
