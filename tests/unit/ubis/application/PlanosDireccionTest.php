<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use src\ubis\application\PlanosDireccion;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\PlanoRepositoryInterface;

final class PlanosDireccionTest extends TestCase
{
    public function test_cada_tipo_de_direccion_va_a_su_repositorio(): void
    {
        $casaDl = $this->createMockForIntersectionOfInterfaces([DireccionCasaDlRepositoryInterface::class, PlanoRepositoryInterface::class]);
        $casaDl->method('planoDownload')->with(12)->willReturn(['plano_nom' => 'casa', 'plano_extension' => 'pdf', 'plano_doc' => '%PDF']);

        $planos = $this->planos(casaDl: $casaDl);

        $this->assertSame(['nom' => 'casa', 'extension' => 'pdf', 'doc' => '%PDF'], $planos->leer('DireccionCdcDl', 12));
        $this->assertTrue($planos->existe('DireccionCdcDl', 12));
    }

    public function test_sin_plano_o_vacio_no_existe(): void
    {
        $centro = $this->createMockForIntersectionOfInterfaces([DireccionCentroRepositoryInterface::class, PlanoRepositoryInterface::class]);
        $centro->method('planoDownload')->willReturnOnConsecutiveCalls(
            ['plano_nom' => null, 'plano_extension' => null, 'plano_doc' => null],
            ['plano_nom' => 'x', 'plano_extension' => 'jpg', 'plano_doc' => ''],
        );

        $planos = $this->planos(centro: $centro);

        $this->assertNull($planos->leer('DireccionCentro', 5));
        $this->assertFalse($planos->existe('DireccionCentro', 5));
    }

    public function test_lee_el_plano_cuando_pdo_lo_da_como_recurso(): void
    {
        $stream = fopen('php://memory', 'r+b');
        fwrite($stream, "\xFF\xD8binario");
        rewind($stream);
        $centroEx = $this->createMockForIntersectionOfInterfaces([DireccionCentroExRepositoryInterface::class, PlanoRepositoryInterface::class]);
        $centroEx->method('planoDownload')->willReturn(['plano_nom' => 'p', 'plano_extension' => 'jpg', 'plano_doc' => $stream]);

        $this->assertSame("\xFF\xD8binario", $this->planos(centroEx: $centroEx)->leer('DireccionCentroEx', 3)['doc'] ?? null);
    }

    public function test_subir_y_borrar_van_al_repositorio_del_tipo(): void
    {
        $casa = $this->createMockForIntersectionOfInterfaces([DireccionCasaRepositoryInterface::class, PlanoRepositoryInterface::class]);
        $casa->expects($this->once())->method('planoUpload')->with(8, 'plano', 'png', 'bytes');
        $casa->expects($this->once())->method('planoBorrar')->with(8);

        $planos = $this->planos(casa: $casa);
        $planos->subir('DireccionCdc', 8, 'plano', 'png', 'bytes');
        $planos->borrar('DireccionCdc', 8);
    }

    public function test_tipo_desconocido_es_un_error(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->planos()->borrar('Ubi', 1);
    }

    private function planos(
        ?DireccionCentroRepositoryInterface $centro = null,
        ?DireccionCentroExRepositoryInterface $centroEx = null,
        ?DireccionCasaRepositoryInterface $casa = null,
        ?DireccionCasaDlRepositoryInterface $casaDl = null,
    ): PlanosDireccion {
        return new PlanosDireccion(
            $centro ?? $this->createMock(DireccionCentroRepositoryInterface::class),
            $this->createMock(DireccionCentroDlRepositoryInterface::class),
            $centroEx ?? $this->createMock(DireccionCentroExRepositoryInterface::class),
            $casa ?? $this->createMock(DireccionCasaRepositoryInterface::class),
            $casaDl ?? $this->createMock(DireccionCasaDlRepositoryInterface::class),
            $this->createMock(DireccionCasaExRepositoryInterface::class),
        );
    }
}
