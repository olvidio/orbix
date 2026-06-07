<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\UbisEditarNormalizeDlData;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

final class UbisEditarNormalizeDlDataTest extends TestCase
{
    public function test_ctrdl_nombre_coincide_devuelve_CentroDl(): void
    {
        $centro = $this->createMock(\src\ubis\domain\entity\CentroDl::class);
        $centro->method('getNombre_ubi')->willReturn('Casa madre');

        $centroRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroRepo->method('findById')->with(9)->willReturn($centro);

        $useCase = new UbisEditarNormalizeDlData(
            $centroRepo,
            $this->createMock(CasaDlRepositoryInterface::class),
        );

        $this->assertSame(
            'CentroDl',
            $useCase->execute(9, 'ctrdl', 'Casa madre', 'PersonaGlobal')
        );
    }

    public function test_ctrdl_nombre_distinto_mantiene_obj_pau(): void
    {
        $centro = $this->createMock(\src\ubis\domain\entity\CentroDl::class);
        $centro->method('getNombre_ubi')->willReturn('Otro nombre');

        $centroRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroRepo->method('findById')->willReturn($centro);

        $useCase = new UbisEditarNormalizeDlData(
            $centroRepo,
            $this->createMock(CasaDlRepositoryInterface::class),
        );

        $this->assertSame(
            'PersonaX',
            $useCase->execute(1, 'ctrdl', 'Cache', 'PersonaX')
        );
    }

    public function test_cdcdl_nombre_coincide_devuelve_CasaDl(): void
    {
        $casa = $this->createMock(\src\ubis\domain\entity\Casa::class);
        $casa->method('getNombre_ubi')->willReturn('Villa');

        $casaRepo = $this->createMock(CasaDlRepositoryInterface::class);
        $casaRepo->method('findById')->with(3)->willReturn($casa);

        $useCase = new UbisEditarNormalizeDlData(
            $this->createMock(CentroDlRepositoryInterface::class),
            $casaRepo,
        );

        $this->assertSame(
            'CasaDl',
            $useCase->execute(3, 'cdcdl', 'Villa', 'Foo')
        );
    }

    public function test_tipo_desconocido_devuelve_obj_pau(): void
    {
        $useCase = new UbisEditarNormalizeDlData(
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(CasaDlRepositoryInterface::class),
        );

        $this->assertSame(
            'Original',
            $useCase->execute(1, 'otro', 'x', 'Original')
        );
    }
}
