<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\CentrosPorFiltroOpciones;
use src\encargossacd\application\EncargoCtrSelectData;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\zonassacd\application\services\CentrosDeZona;
use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;

final class EncargoCtrSelectDataTest extends TestCase
{
    public function test_opciones_preservan_orden_del_backend_no_por_id_en_json(): void
    {
        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->method('getArrayCentros')->willReturn([
            1003 => 'Centro Z',
            501 => 'Centro A',
            502 => 'Centro B',
        ]);

        $useCase = new EncargoCtrSelectData(
            $this->centrosPorFiltro($centroDl),
        );

        $out = $useCase->execute(501, EncargoGrupo::CENTRO_SV, 0);

        $this->assertSame([
            ['1003', 'Centro Z'],
            ['501', 'Centro A'],
            ['502', 'Centro B'],
        ], $out['opciones']);
    }

    public function test_cgi_devuelve_blanco_true(): void
    {
        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->method('getArrayCentros')->willReturn([
            1003 => 'Colegio Z',
            501 => 'Colegio A',
        ]);

        $useCase = new EncargoCtrSelectData(
            $this->centrosPorFiltro($centroDl),
        );

        $out = $useCase->execute(0, EncargoGrupo::CGI, 0);

        $this->assertTrue($out['blanco']);
        $this->assertSame('fnjs_ver_ficha()', $out['action']);
    }

    public function test_filtro_sv_no_usa_id_zona_si_filtro_no_es_zonas(): void
    {
        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->expects($this->once())
            ->method('getArrayCentros')
            ->with($this->stringContains("tipo_ctr ~ '^a|n|s[^s]|of'"))
            ->willReturn([
                501 => 'Centro A',
            ]);

        $useCase = new EncargoCtrSelectData(
            $this->centrosPorFiltro($centroDl),
        );

        $out = $useCase->execute(501, EncargoGrupo::CENTRO_SV, 99);

        $this->assertSame('501', $out['selected']);
        $this->assertSame([['501', 'Centro A']], $out['opciones']);
    }

    public function test_action_vacio_para_encargo_ver(): void
    {
        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->method('getArrayCentros')->willReturn([]);

        $useCase = new EncargoCtrSelectData(
            $this->centrosPorFiltro($centroDl),
        );

        $out = $useCase->execute(0, EncargoGrupo::CENTRO_SV, 0, '');

        $this->assertSame('', $out['action']);
    }

    public function test_sin_id_ubi_devuelve_blanco_true(): void
    {
        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->method('getArrayCentros')->willReturn([
            501 => 'Centro A',
        ]);

        $useCase = new EncargoCtrSelectData(
            $this->centrosPorFiltro($centroDl),
        );

        $out = $useCase->execute(0, EncargoGrupo::CENTRO_SV, 0);

        $this->assertTrue($out['blanco']);
        $this->assertSame('', $out['selected']);
    }

    public function test_con_id_ubi_no_fuerza_blanco_en_sv(): void
    {
        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->method('getArrayCentros')->willReturn([
            501 => 'Centro A',
        ]);

        $useCase = new EncargoCtrSelectData(
            $this->centrosPorFiltro($centroDl),
        );

        $out = $useCase->execute(501, EncargoGrupo::CENTRO_SV, 0);

        $this->assertFalse($out['blanco']);
    }

    public function test_zonas_misas_filtra_por_id_ubi_de_zonas_ctr(): void
    {
        $zonaCtr = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtr->expects($this->once())->method('idUbisDeZona')->with(9)->willReturn([1042, 2005]);
        $centrosDeZona = new CentrosDeZona($zonaCtr);

        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->expects($this->once())
            ->method('getArrayCentros')
            ->with($this->stringContains('id_ubi IN (1042,2005)'))
            ->willReturn([1042 => 'Centro DL']);

        $centroEllas = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllas->expects($this->once())
            ->method('getArrayCentros')
            ->with($this->stringContains('id_ubi IN (1042,2005)'))
            ->willReturn([2005 => 'Centro SF']);

        $out = (new EncargoCtrSelectData(
            new CentrosPorFiltroOpciones($centroDl, $centroEllas, $centrosDeZona),
        ))->execute(0, EncargoGrupo::ZONAS_MISAS, 9);

        $this->assertSame([['1042', 'Centro DL'], ['2005', 'Centro SF']], $out['opciones']);
    }

    private function centrosPorFiltro(CentroDlRepositoryInterface $centroDl): CentrosPorFiltroOpciones
    {
        return new CentrosPorFiltroOpciones(
            $centroDl,
            $this->createStub(CentroEllasRepositoryInterface::class),
            new CentrosDeZona($this->createStub(ZonaCtrRepositoryInterface::class)),
        );
    }
}
