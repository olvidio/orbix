<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadLugar;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Casa;

final class ActividadLugarApplicationTest extends TestCase
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

    public function test_get_filtro_lugar_con_dl(): void
    {
        $casa = $this->createMock(Casa::class);
        $casa->method('getDl')->willReturn('dlb');
        $casa->method('getRegion')->willReturn('r1');

        $casaRepo = $this->createMock(CasaRepositoryInterface::class);
        $casaRepo->method('findById')->with(5)->willReturn($casa);

        $GLOBALS['container'] = $this->containerMap([
            CasaRepositoryInterface::class => $casaRepo,
        ]);

        $o = new ActividadLugar();
        $this->assertSame('dl|dlb', $o->getFiltroLugar(5));
    }

    public function test_get_filtro_lugar_sin_dl_usa_region(): void
    {
        $casa = $this->createMock(Casa::class);
        $casa->method('getDl')->willReturn('');
        $casa->method('getRegion')->willReturn('Bel');

        $casaRepo = $this->createMock(CasaRepositoryInterface::class);
        $casaRepo->method('findById')->willReturn($casa);

        $GLOBALS['container'] = $this->containerMap([
            CasaRepositoryInterface::class => $casaRepo,
        ]);

        $o = new ActividadLugar();
        $this->assertSame('r|Bel', $o->getFiltroLugar(1));
    }

    public function test_get_lugares_posibles_vacio_si_entrada_vacia(): void
    {
        $GLOBALS['container'] = $this->containerMap([]);
        $o = new ActividadLugar();
        $this->assertSame([], $o->getLugaresPosibles(''));
    }

    public function test_get_lugares_posibles_una_casa_y_centro(): void
    {
        $casaRepo = $this->createMock(CasaRepositoryInterface::class);
        $casaRepo->method('getArrayCasas')->willReturn([10 => 'Casa A']);

        $centroRepo = $this->createMock(CentroRepositoryInterface::class);
        $centroRepo->method('getArrayCentrosCdc')->willReturn([20 => 'Ctr B']);

        $GLOBALS['container'] = $this->containerMap([
            CasaRepositoryInterface::class => $casaRepo,
            CentroRepositoryInterface::class => $centroRepo,
        ]);

        $o = new ActividadLugar();
        $o->setSsfsv('sv');
        $out = $o->getLugaresPosibles('dl|dlb');

        $this->assertSame([10 => 'Casa A', 20 => 'Ctr B'], $out);
    }

    /** @param array<class-string, object> $map */
    private function containerMap(array $map): object
    {
        return new class($map) {
            public function __construct(private readonly array $map) {}

            public function get(string $id): object
            {
                if (!isset($this->map[$id])) {
                    throw new \RuntimeException('Missing DI: ' . $id);
                }
                return $this->map[$id];
            }
        };
    }
}
