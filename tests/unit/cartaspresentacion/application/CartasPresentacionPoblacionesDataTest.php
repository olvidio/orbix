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
    public function test_filtro_desconocido_devuelve_opciones_vacias(): void
    {
        $useCase = new CartasPresentacionPoblacionesData(
            $this->createMock(DireccionCentroRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(DireccionCentroDlRepositoryInterface::class),
            $this->createMock(RelacionCentroDlDireccionRepositoryInterface::class),
        );

        $rta = $useCase->execute(['filtro' => '']);
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

        $useCase = new CartasPresentacionPoblacionesData(
            $repo,
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(DireccionCentroDlRepositoryInterface::class),
            $this->createMock(RelacionCentroDlDireccionRepositoryInterface::class),
        );

        $rta = $useCase->execute(['filtro' => 'get_H']);
        $this->assertSame([['Madrid', 'Madrid']], $rta['opciones']);
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

        $useCase = new CartasPresentacionPoblacionesData(
            $this->createMock(DireccionCentroRepositoryInterface::class),
            $repoCentro,
            $repoDir,
            $repoCtrx,
        );

        $rta = $useCase->execute(['filtro' => 'get_dl']);
        $this->assertSame([['Salamanca', 'Salamanca']], $rta['opciones']);
    }
}
