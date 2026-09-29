<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\shared\security\HashB;
use src\ubis\application\DireccionesEditarData;
use src\ubis\application\DireccionesResolver;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrRepositoryInterface;
use src\ubis\domain\entity\Centro;

final class DireccionesEditarDataTest extends TestCase
{
    public function test_modo_nuevo_emite_ctx_guardar_y_ctx_quitar_atados_al_contexto(): void
    {
        $centro = $this->createMock(Centro::class);
        $centro->method('getDl')->willReturn('bcn');

        $centroRepo = $this->createMock(CentroRepositoryInterface::class);
        $centroRepo->method('findById')->with(501)->willReturn($centro);

        $useCase = new DireccionesEditarData(
            $this->resolver($centroRepo),
            $this->ubiRepositoryResolver(),
        );

        $data = $useCase->execute(501, 'nuevo', 'DireccionCentro', '', 0, '');

        $this->assertFalse($data['sin_direccion']);
        $this->assertSame(-1, $data['idx']);

        $contexto = ['obj_dir' => 'DireccionCentro', 'id_ubi' => 501, 'idx' => -1, 'id_direccion' => ''];
        $this->assertSame($contexto, HashB::open($data['ctx_guardar'], 'direccion_update'));
        $this->assertSame($contexto, HashB::open($data['ctx_quitar'], 'direcciones_quitar'));
    }

    public function test_ubi_no_encontrado_no_emite_ctx(): void
    {
        $centroRepo = $this->createMock(CentroRepositoryInterface::class);
        $centroRepo->method('findById')->willReturn(null);

        $useCase = new DireccionesEditarData(
            $this->resolver($centroRepo),
            $this->ubiRepositoryResolver(),
        );

        $data = $useCase->execute(999, 'nuevo', 'DireccionCentro', '', 0, '');

        $this->assertTrue($data['sin_direccion']);
        $this->assertArrayNotHasKey('ctx_guardar', $data);
    }

    private function resolver(?CentroRepositoryInterface $centroRepo = null): DireccionesResolver
    {
        return new DireccionesResolver(
            $this->createMock(DireccionCentroRepositoryInterface::class),
            $this->createMock(DireccionCentroDlRepositoryInterface::class),
            $this->createMock(DireccionCentroExRepositoryInterface::class),
            $this->createMock(DireccionCasaRepositoryInterface::class),
            $this->createMock(DireccionCasaDlRepositoryInterface::class),
            $this->createMock(DireccionCasaExRepositoryInterface::class),
            $centroRepo ?? $this->createMock(CentroRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(CentroExRepositoryInterface::class),
            $this->createMock(CasaRepositoryInterface::class),
            $this->createMock(CasaDlRepositoryInterface::class),
            $this->createMock(CasaExRepositoryInterface::class),
        );
    }

    private function ubiRepositoryResolver(): UbiRepositoryResolver
    {
        return new UbiRepositoryResolver(
            $this->createMock(CentroRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(CentroExRepositoryInterface::class),
            $this->createMock(CasaRepositoryInterface::class),
            $this->createMock(CasaDlRepositoryInterface::class),
            $this->createMock(CasaExRepositoryInterface::class),
            $this->createMock(TelecoCtrRepositoryInterface::class),
            $this->createMock(TelecoCtrDlRepositoryInterface::class),
            $this->createMock(TelecoCtrExRepositoryInterface::class),
            $this->createMock(TelecoCdcRepositoryInterface::class),
            $this->createMock(TelecoCdcDlRepositoryInterface::class),
            $this->createMock(TelecoCdcExRepositoryInterface::class),
            $this->createMock(DireccionCentroRepositoryInterface::class),
            $this->createMock(DireccionCentroDlRepositoryInterface::class),
            $this->createMock(DireccionCentroExRepositoryInterface::class),
            $this->createMock(DireccionCasaRepositoryInterface::class),
            $this->createMock(DireccionCasaDlRepositoryInterface::class),
            $this->createMock(DireccionCasaExRepositoryInterface::class),
            $this->createMock(RelacionCentroDireccionRepositoryInterface::class),
            $this->createMock(RelacionCentroDlDireccionRepositoryInterface::class),
            $this->createMock(RelacionCentroExDireccionRepositoryInterface::class),
            $this->createMock(RelacionCasaDireccionRepositoryInterface::class),
            $this->createMock(RelacionCasaDlDireccionRepositoryInterface::class),
            $this->createMock(RelacionCasaExDireccionRepositoryInterface::class),
        );
    }
}
