<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\CtrFichaData;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;

final class CtrFichaDataTest extends TestCase
{
    private EncargoAplicacionService&MockObject $aplicacion;
    private CentroDlRepositoryInterface&MockObject $centrosDl;
    private CentroEllasRepositoryInterface&MockObject $centrosEllas;
    private CtrFichaData $useCase;

    protected function setUp(): void
    {
        $this->aplicacion = $this->createMock(EncargoAplicacionService::class);
        $this->aplicacion->method('getArraySeccion')->willReturn(['1' => 'sv', '2' => 'sf']);
        $this->centrosDl = $this->createMock(CentroDlRepositoryInterface::class);
        $this->centrosEllas = $this->createMock(CentroEllasRepositoryInterface::class);
        $this->useCase = new CtrFichaData($this->aplicacion, $this->centrosDl, $this->centrosEllas);
    }

    private function centroDl(string $tipoUbi, string $tipoCtr): CentroDl
    {
        $c = $this->createMock(CentroDl::class);
        $c->method('getTipo_ubi')->willReturn($tipoUbi);
        $c->method('getTipo_ctr')->willReturn($tipoCtr);

        return $c;
    }

    public function test_sin_centro_conserva_el_filtro_y_devuelve_las_secciones(): void
    {
        $this->centrosDl->expects($this->never())->method('findById');
        $this->centrosEllas->expects($this->never())->method('findById');

        $out = $this->useCase->execute(0, 7);

        $this->assertSame(['filtro_ctr' => 7, 'opciones_seccion' => ['1' => 'sv', '2' => 'sf']], $out);
    }

    /**
     * @dataProvider tiposDeCentroProvider
     */
    public function test_el_filtro_depende_del_tipo_de_centro(string $tipoCtr, int $esperado): void
    {
        $this->centrosDl->method('findById')->with(1001)->willReturn($this->centroDl('ctr', $tipoCtr));

        $this->assertSame($esperado, $this->useCase->execute(1001, 99)['filtro_ctr']);
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function tiposDeCentroProvider(): iterable
    {
        foreach (['a', 'n', 's', 'aj', 'am', 'nj', 'nm', 'sj', 'sm'] as $t) {
            yield "sv: $t" => [$t, EncargoGrupo::CENTRO_SV];
        }
        yield 'sssc' => ['ss', EncargoGrupo::CENTRO_SSSC];
        yield 'iglesia' => ['igloc', EncargoGrupo::IGL];
        yield 'cgi: cgioc' => ['cgioc', EncargoGrupo::CGI];
        yield 'cgi: oc' => ['oc', EncargoGrupo::CGI];
        yield 'tipo desconocido: se queda el filtro recibido' => ['zz', 99];
        yield 'tipo vacío: se queda el filtro recibido' => ['', 99];
        yield 'mayúsculas no coinciden' => ['A', 99];
    }

    public function test_un_centro_de_ellas_es_siempre_el_grupo_sf(): void
    {
        $c = $this->createMock(CentroEllas::class);
        $c->method('getTipo_ubi')->willReturn('ctrsf');
        $c->method('getTipo_ctr')->willReturn('n');
        $this->centrosEllas->expects($this->once())->method('findById')->with(2001)->willReturn($c);
        $this->centrosDl->expects($this->never())->method('findById');

        $this->assertSame(EncargoGrupo::CENTRO_SF, $this->useCase->execute(2001, 1)['filtro_ctr']);
    }

    public function test_el_tipo_ctrsf_manda_sobre_el_tipo_de_centro(): void
    {
        $this->centrosDl->method('findById')->willReturn($this->centroDl('ctrsf', 'n'));

        $this->assertSame(EncargoGrupo::CENTRO_SF, $this->useCase->execute(1001, 1)['filtro_ctr']);
    }

    public function test_el_primer_digito_del_id_elige_el_repositorio(): void
    {
        $this->centrosEllas->expects($this->once())->method('findById')->with(2999)->willReturn(null);
        $this->centrosDl->expects($this->once())->method('findById')->with(3001)->willReturn(null);

        $this->useCase->execute(2999, 5);
        $this->useCase->execute(3001, 5);
    }

    public function test_un_centro_inexistente_conserva_el_filtro(): void
    {
        $this->centrosDl->method('findById')->willReturn(null);

        $this->assertSame(5, $this->useCase->execute(1001, 5)['filtro_ctr']);
    }
}
