<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoComprobacionesCtr;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;

final class EncargoComprobacionesCtrTest extends TestCase
{
    private CentroDlRepositoryInterface&MockObject $centrosDl;
    private CentroEllasRepositoryInterface&MockObject $centrosEllas;
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoSacdRepositoryInterface&MockObject $encargosSacd;
    private EncargoComprobacionesCtr $useCase;

    protected function setUp(): void
    {
        $this->centrosDl = $this->createMock(CentroDlRepositoryInterface::class);
        $this->centrosEllas = $this->createMock(CentroEllasRepositoryInterface::class);
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->encargosSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        $this->useCase = new EncargoComprobacionesCtr($this->centrosDl, $this->centrosEllas, $this->encargos, $this->encargosSacd);
    }

    private function encargo(int $idEnc, ?int $idUbi): Encargo
    {
        $e = new Encargo();
        $e->setId_enc($idEnc);
        $e->setId_ubi($idUbi);

        return $e;
    }

    private function dl(bool $activo): CentroDl
    {
        $c = $this->createMock(CentroDl::class);
        $c->method('isActive')->willReturn($activo);

        return $c;
    }

    private function ellas(bool $activo): CentroEllas
    {
        $c = $this->createMock(CentroEllas::class);
        $c->method('isActive')->willReturn($activo);

        return $c;
    }

    public function test_elimina_solo_los_encargos_de_centros_inactivos_y_cuenta_sv_y_sf(): void
    {
        $dlInactivo = $this->encargo(1, 1001);
        $dlActivo = $this->encargo(2, 1002);
        $sfInactivo = $this->encargo(3, 2001);
        $sfActivo = $this->encargo(4, 2002);
        $this->encargos->method('matching')->willReturn([$dlInactivo, $dlActivo, $sfInactivo, $sfActivo]);
        $this->centrosDl->method('findById')->willReturnMap([[1001, $this->dl(false)], [1002, $this->dl(true)]]);
        $this->centrosEllas->method('findById')->willReturnMap([[2001, $this->ellas(false)], [2002, $this->ellas(true)]]);
        $eliminados = [];
        $this->encargos->expects($this->exactly(2))->method('Eliminar')->willReturnCallback(function (Encargo $e) use (&$eliminados): bool {
            $eliminados[] = $e->getId_enc();

            return true;
        });
        $this->encargosSacd->method('deleteEncargos')->willReturn('');

        $out = $this->useCase->ejecutar();

        $this->assertSame([1, 3], $eliminados);
        $this->assertStringContainsString('1 encargos de centros sv', $out['texto']);
        $this->assertStringContainsString('1 encargos de centros sf', $out['texto']);
    }

    public function test_el_prefijo_1_es_un_centro_de_la_dl_y_cualquier_otro_de_ellas(): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargo(1, 1001), $this->encargo(2, 3001)]);
        $this->centrosDl->expects($this->once())->method('findById')->with(1001)->willReturn($this->dl(true));
        $this->centrosEllas->expects($this->once())->method('findById')->with(3001)->willReturn($this->ellas(true));
        $this->encargosSacd->method('deleteEncargos')->willReturn('');

        $this->useCase->ejecutar();
    }

    public function test_un_centro_inexistente_o_un_encargo_sin_centro_no_se_toca(): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargo(1, 1001), $this->encargo(2, 2001), $this->encargo(3, null), $this->encargo(4, 0)]);
        $this->centrosDl->method('findById')->willReturn(null);
        $this->centrosEllas->method('findById')->willReturn(null);
        $this->encargos->expects($this->never())->method('Eliminar');
        $this->encargosSacd->method('deleteEncargos')->willReturn('');

        $out = $this->useCase->ejecutar();

        $this->assertStringContainsString('0 encargos de centros sv', $out['texto']);
        $this->assertStringContainsString('0 encargos de centros sf', $out['texto']);
    }

    public function test_limpia_los_encargos_sacd_con_los_ids_de_los_encargos_existentes_y_anade_su_texto(): void
    {
        // Primera consulta (encargos con centro) y segunda (todos): la del limpiado de sacd.
        $this->encargos->method('matching')->willReturnOnConsecutiveCalls([], [$this->encargo(10, 1001), $this->encargo(11, null)]);
        $this->encargosSacd->expects($this->once())->method('deleteEncargos')->with([10, 11])->willReturn('limpiados 3');

        $out = $this->useCase->ejecutar();

        $this->assertStringEndsWith('limpiados 3', $out['texto']);
    }

    public function test_sin_encargos_pasa_una_lista_vacia_a_la_limpieza(): void
    {
        $this->encargos->method('matching')->willReturn([]);
        $this->encargosSacd->expects($this->once())->method('deleteEncargos')->with([])->willReturn('');

        $this->assertArrayHasKey('texto', $this->useCase->ejecutar());
    }
}
