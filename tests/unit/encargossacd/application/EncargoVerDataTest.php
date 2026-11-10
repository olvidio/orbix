<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoVerData;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoTipo;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\shared\domain\criteria\Criteria;
use src\shared\security\HashB;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use Tests\unit\planning\support\DescribeCriteria;

final class EncargoVerDataTest extends TestCase
{
    private EncargoAplicacionService&MockObject $aplicacion;
    private CentroDlRepositoryInterface&MockObject $centrosDl;
    private CentroEllasRepositoryInterface&MockObject $centrosEllas;
    private EncargoRepositoryInterface&MockObject $encargos;
    private LocalRepositoryInterface&MockObject $locales;
    private ZonaRepositoryInterface&MockObject $zonas;
    private EncargoTipoRepositoryInterface&MockObject $tipos;
    private EncargoVerData $useCase;

    protected function setUp(): void
    {
        $this->aplicacion = $this->createMock(EncargoAplicacionService::class);
        $this->aplicacion->method('getArraySeccion')->willReturn(['1' => 'sv', '2' => 'sf']);
        $this->centrosDl = $this->createMock(CentroDlRepositoryInterface::class);
        $this->centrosEllas = $this->createMock(CentroEllasRepositoryInterface::class);
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->locales = $this->createMock(LocalRepositoryInterface::class);
        $this->locales->method('getArrayLocales')->willReturn(['es_ES.UTF-8' => 'español']);
        $this->zonas = $this->createMock(ZonaRepositoryInterface::class);
        $this->zonas->method('getArrayZonas')->willReturn([3 => 'Zona 3']);
        $this->tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $this->tipos->method('encargo_de_tipo')->willReturn(['grupo' => '2']);
        $this->tipos->method('matching')->willReturn([]);
        $this->useCase = new EncargoVerData($this->aplicacion, $this->centrosDl, $this->centrosEllas, $this->encargos, $this->locales, $this->zonas, $this->tipos);
    }

    private function encargo(int $grupo, ?int $idUbi = 1001): Encargo
    {
        $e = new Encargo();
        $e->setId_enc(7);
        $e->setId_tipo_enc(2100);
        $e->setGrupo_encargo($grupo);
        $e->setId_ubi($idUbi);
        $e->setId_zona(3);
        $e->setDesc_enc('Misa diaria');
        $e->setDesc_lugar('Capilla');
        $e->setIdioma_enc('es_ES.UTF-8');

        return $e;
    }

    /** @return array<string, mixed> */
    private function ejecutar(string $que = 'editar', int $idEnc = 7, int $idTipo = 0, string $grupo = '', string $filtro = '', string $desc = '', string $lugar = '', int $zona = 0): array
    {
        return $this->useCase->execute($que, $idEnc, $idTipo, $grupo, $filtro, $desc, $lugar, $zona);
    }

    public function test_editar_carga_los_datos_del_encargo(): void
    {
        $this->encargos->method('findById')->with(7)->willReturn($this->encargo(1));

        $out = $this->ejecutar('editar');

        $this->assertSame('editar', $out['que']);
        $this->assertSame(7, $out['id_enc']);
        $this->assertSame(2100, $out['id_tipo_enc']);
        $this->assertSame('Misa diaria', $out['desc_enc']);
        $this->assertSame('Capilla', $out['desc_lugar']);
        $this->assertSame('es_ES.UTF-8', $out['idioma_enc']);
        $this->assertSame(1001, $out['id_ubi']);
        $this->assertSame(3, $out['id_zona']);
        $this->assertSame('1', $out['filtro_ctr'], 'el filtro es el grupo del encargo');
    }

    public function test_el_modo_vacio_tambien_carga_el_encargo(): void
    {
        $this->encargos->expects($this->once())->method('findById')->willReturn($this->encargo(1));

        $this->assertSame('Misa diaria', $this->ejecutar('')['desc_enc']);
    }

    public function test_nuevo_no_carga_el_encargo_y_conserva_lo_recibido(): void
    {
        $this->encargos->expects($this->never())->method('findById');

        $out = $this->ejecutar('nuevo', 0, 2100, '', '1', 'Texto', 'Lugar', 5);

        $this->assertSame('Texto', $out['desc_enc']);
        $this->assertSame('Lugar', $out['desc_lugar']);
        $this->assertSame(5, $out['id_zona']);
        $this->assertSame('1', $out['filtro_ctr']);
        $this->assertSame(0, $out['id_ubi']);
        $this->assertSame('', $out['idioma_enc']);
    }

    public function test_un_encargo_inexistente_conserva_lo_recibido(): void
    {
        $this->encargos->method('findById')->willReturn(null);

        $out = $this->ejecutar('editar', 7, 2100, '', '1', 'Recibido');

        $this->assertSame('Recibido', $out['desc_enc']);
        $this->assertSame(0, $out['id_ubi']);
    }

    public function test_el_grupo_8_fuerza_el_filtro_8(): void
    {
        $this->encargos->method('findById')->willReturn(null);

        $this->assertSame('8', $this->ejecutar('nuevo', 0, 0, '8', '1')['filtro_ctr']);
    }

    // ---- filtro calculado desde el centro (grupo del encargo = 0) ------------------------------------------

    /**
     * @dataProvider tiposDeCentroProvider
     */
    public function test_encargo_personal_con_centro_calcula_el_filtro_por_el_tipo_de_centro(string $tipoCtr, string $esperado): void
    {
        $this->encargos->method('findById')->willReturn($this->encargo(0, 1001));
        $c = $this->createMock(CentroDl::class);
        $c->method('getTipo_ubi')->willReturn('ctr');
        $c->method('getTipo_ctr')->willReturn($tipoCtr);
        $this->centrosDl->method('findById')->with(1001)->willReturn($c);

        $this->assertSame($esperado, $this->ejecutar()['filtro_ctr']);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function tiposDeCentroProvider(): iterable
    {
        foreach (['aj', 'am', 'nj', 'njce', 'nm', 'sj', 'sm', 'sjce'] as $t) {
            yield "sv: $t" => [$t, (string) EncargoGrupo::CENTRO_SV];
        }
        yield 'sssc' => ['ss', (string) EncargoGrupo::CENTRO_SSSC];
        yield 'iglesia igloc' => ['igloc', (string) EncargoGrupo::IGL];
        yield 'iglesia igl' => ['igl', (string) EncargoGrupo::IGL];
        yield 'cgi' => ['cgioc', (string) EncargoGrupo::CGI];
        yield 'oc' => ['oc', (string) EncargoGrupo::CGI];
        yield 'desconocido' => ['zz', '0'];
        // Hallazgo: a, n y s (sin sufijo) no se mapean aquí, a diferencia de CtrFichaData.
        yield 'a sin sufijo' => ['a', '0'];
        yield 'n sin sufijo' => ['n', '0'];
    }

    public function test_un_centro_sf_es_el_grupo_sf_y_se_busca_en_el_repositorio_de_ellas(): void
    {
        $this->encargos->method('findById')->willReturn($this->encargo(0, 2001));
        $c = $this->createMock(CentroEllas::class);
        $c->method('getTipo_ubi')->willReturn('ctrsf');
        $c->method('getTipo_ctr')->willReturn('n');
        $this->centrosEllas->expects($this->once())->method('findById')->with(2001)->willReturn($c);
        $this->centrosDl->expects($this->never())->method('findById');

        $this->assertSame((string) EncargoGrupo::CENTRO_SF, $this->ejecutar()['filtro_ctr']);
    }

    public function test_un_centro_inexistente_deja_el_filtro_en_cero(): void
    {
        $this->encargos->method('findById')->willReturn($this->encargo(0, 1001));
        $this->centrosDl->method('findById')->willReturn(null);

        $this->assertSame('0', $this->ejecutar()['filtro_ctr']);
    }

    public function test_un_encargo_personal_sin_centro_no_consulta_centros(): void
    {
        $this->encargos->method('findById')->willReturn($this->encargo(0, null));
        $this->centrosDl->expects($this->never())->method('findById');
        $this->centrosEllas->expects($this->never())->method('findById');

        $this->assertSame('0', $this->ejecutar()['filtro_ctr']);
    }

    // ---- tipos y grupos ---------------------------------------------------------------------------------------

    public function test_con_tipo_conocido_el_grupo_es_el_primer_digito_y_se_listan_los_tipos_del_grupo(): void
    {
        $this->encargos->method('findById')->willReturn(null);
        $t = $this->createMock(EncargoTipo::class);
        $t->method('getId_tipo_enc')->willReturn(2100);
        $t->method('getTipo_enc')->willReturn('Centro');
        $tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $tipos->method('encargo_de_tipo')->willReturn(['grupo' => ['1' => 'sv', '2' => 'sf']]);
        $tipos->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_tipo_enc matches ^2'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([$t]);
        $uc = new EncargoVerData($this->aplicacion, $this->centrosDl, $this->centrosEllas, $this->encargos, $this->locales, $this->zonas, $tipos);

        $out = $uc->execute('nuevo', 0, 2100, '', '', '', '', 0);

        $this->assertSame('2', $out['grupo']);
        $this->assertSame(['2100' => 'Centro'], $out['posibles_encargo_tipo']);
        $this->assertSame(['1' => 'sv', '2' => 'sf'], $out['grupo_posibles']);
    }

    public function test_sin_tipo_se_resuelve_con_el_grupo_recibido(): void
    {
        $this->encargos->method('findById')->willReturn(null);
        $tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $tipos->expects($this->once())->method('id_tipo_encargo')->with('3', '...')->willReturn('3000');
        $tipos->method('encargo_de_tipo')->willReturn(['grupo' => []]);
        $tipos->method('matching')->willReturn([]);
        $uc = new EncargoVerData($this->aplicacion, $this->centrosDl, $this->centrosEllas, $this->encargos, $this->locales, $this->zonas, $tipos);

        $out = $uc->execute('nuevo', 0, 0, '3', '', '', '', 0);

        $this->assertSame(3000, $out['id_tipo_enc']);
        $this->assertSame('3', $out['grupo']);
    }

    public function test_sin_grupo_no_lista_tipos(): void
    {
        $this->encargos->method('findById')->willReturn(null);
        $tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $tipos->method('id_tipo_encargo')->willReturn('0');
        $tipos->method('encargo_de_tipo')->willReturn(['grupo' => []]);
        $tipos->expects($this->never())->method('matching');
        $uc = new EncargoVerData($this->aplicacion, $this->centrosDl, $this->centrosEllas, $this->encargos, $this->locales, $this->zonas, $tipos);

        $out = $uc->execute('nuevo', 0, 0, '', '', '', '', 0);

        $this->assertSame([], $out['posibles_encargo_tipo']);
        $this->assertSame(0, $out['id_tipo_enc']);
    }

    // ---- opciones y tokens ------------------------------------------------------------------------------------

    public function test_las_opciones_salen_con_claves_y_valores_en_texto(): void
    {
        $this->encargos->method('findById')->willReturn(null);

        $out = $this->ejecutar('nuevo', 0, 2100);

        $this->assertSame(['1' => 'sv', '2' => 'sf'], $out['opciones_seccion']);
        $this->assertSame(['3' => 'Zona 3'], $out['opciones_zonas']);
        $this->assertSame(['es_ES.UTF-8' => 'español'], $out['opciones_locales']);
    }

    public function test_las_opciones_no_escalares_se_descartan(): void
    {
        $this->encargos->method('findById')->willReturn(null);
        $zonas = $this->createMock(ZonaRepositoryInterface::class);
        $zonas->method('getArrayZonas')->willReturn([3 => 'Zona 3', 4 => ['raro'], 5 => 7]);
        $uc = new EncargoVerData($this->aplicacion, $this->centrosDl, $this->centrosEllas, $this->encargos, $this->locales, $zonas, $this->tipos);

        $this->assertSame(['3' => 'Zona 3', '5' => '7'], $uc->execute('nuevo', 0, 2100, '', '', '', '', 0)['opciones_zonas']);
    }

    public function test_los_tokens_de_nuevo_y_editar(): void
    {
        $this->encargos->method('findById')->willReturn(null);

        $conId = $this->ejecutar('editar', 7, 2100);
        $sinId = $this->ejecutar('nuevo', 0, 2100);

        $this->assertNotSame('', $conId['ctx_nuevo']);
        $this->assertNotSame('', $sinId['ctx_nuevo'], 'el token de alta no depende del encargo');
        $this->assertSame(['id_enc' => 7], HashB::open($conId['ctx_editar'], 'encargo_ver_editar'));
        $this->assertSame('', $sinId['ctx_editar'], 'sin encargo no hay token de edición');
    }
}
