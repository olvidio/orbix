<?php

namespace Tests\unit\cartaspresentacion\application;

use PHPUnit\Framework\TestCase;
use src\cartaspresentacion\application\CartasPresentacionPoblacionesData;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\Direccion;

final class CartasPresentacionPoblacionesDataTest extends TestCase
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

    public function test_filtro_desconocido_devuelve_opciones_vacias(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            DireccionCentroRepositoryInterface::class => $this->createMock(DireccionCentroRepositoryInterface::class),
        ]);

        $rta = CartasPresentacionPoblacionesData::execute(['filtro' => '']);
        $this->assertSame('poblacion_sel', $rta['id']);
        $this->assertSame([], $rta['opciones']);
    }

    public function test_get_H_usa_repositorio_direcciones(): void
    {
        $repo = $this->createMock(DireccionCentroRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayPoblaciones')
            ->with($this->stringContains("pais ILIKE 'españa'"))
            ->willReturn(['Madrid' => 'Madrid']);

        $GLOBALS['container'] = $this->containerFromMap([
            DireccionCentroRepositoryInterface::class => $repo,
        ]);

        $rta = CartasPresentacionPoblacionesData::execute(['filtro' => 'get_H']);
        $this->assertSame(['Madrid' => 'Madrid'], $rta['opciones']);
    }

    public function test_get_dl_agrupa_poblaciones(): void
    {
        $oCentro = $this->createMock(CentroDl::class);
        $oCentro->method('getId_ubi')->willReturn(10);

        $oDir = $this->createMock(Direccion::class);
        $oDir->method('getPoblacion')->willReturn('Salamanca');

        $repoCentro = $this->createMock(CentroDlRepositoryInterface::class);
        $repoCentro->method('getCentros')->willReturn([$oCentro]);

        $repoCtrx = $this->createMock(RelacionCentroDlDireccionRepositoryInterface::class);
        $repoCtrx->method('getDireccionesPorUbi')->with(10)->willReturn([['id_direccion' => 77]]);

        $repoDir = $this->createMock(DireccionCentroDlRepositoryInterface::class);
        $repoDir->method('findById')->with(77)->willReturn($oDir);

        $GLOBALS['container'] = $this->containerFromMap([
            DireccionCentroRepositoryInterface::class => $this->createMock(DireccionCentroRepositoryInterface::class),
            CentroDlRepositoryInterface::class => $repoCentro,
            DireccionCentroDlRepositoryInterface::class => $repoDir,
            RelacionCentroDlDireccionRepositoryInterface::class => $repoCtrx,
        ]);

        $rta = CartasPresentacionPoblacionesData::execute(['filtro' => 'get_dl']);
        $this->assertSame(['Salamanca' => 'Salamanca'], $rta['opciones']);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
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
