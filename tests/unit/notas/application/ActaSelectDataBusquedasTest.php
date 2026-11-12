<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\application\ActaSelectData;
use src\notas\application\support\ActaPrefijosDeEsquema;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaExRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use Tests\support\DescribeCriteria;

/**
 * `ActaSelectData`: qué repositorio y qué criterio se usan según lo que se busca (nada, un número, número/año,
 * texto) y el ámbito (dl o región del stgr).
 */
final class ActaSelectDataBusquedasTest extends TestCase
{
    private mixed $configAnterior;
    /** @var array<string, mixed> */
    private array $authAnterior;

    private ActaRepositoryInterface&MockObject $actas;
    private ActaDlRepositoryInterface&MockObject $actasDl;
    private ActaExRepositoryInterface&MockObject $actasEx;
    /** @var array<string, list<Criteria>> */
    private array $consultas = ['actas' => [], 'dl' => [], 'ex' => []];
    /** @var list<Acta> */
    private array $resultadoDl = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $this->authAnterior = $_SESSION['session_auth'] ?? [];
        $_SESSION['session_auth'] = array_merge($this->authAnterior, ['esquema' => 'H-dlbv', 'sfsv' => 1]);
        $this->ambito('dl');

        $this->actas = $this->createMock(ActaRepositoryInterface::class);
        $this->actasDl = $this->createMock(ActaDlRepositoryInterface::class);
        $this->actasEx = $this->createMock(ActaExRepositoryInterface::class);
        $this->actas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->consultas['actas'][] = $c;

            return [];
        });
        $this->actasDl->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->consultas['dl'][] = $c;

            return $this->resultadoDl;
        });
        $this->actasEx->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->consultas['ex'][] = $c;

            return [];
        });
    }

    protected function tearDown(): void
    {
        $_SESSION['oConfig'] = $this->configAnterior;
        $_SESSION['session_auth'] = $this->authAnterior;
        parent::tearDown();
    }

    private function ambito(string $ambito): void
    {
        $_SESSION['oConfig'] = new ConfigSnapshot(
            null, null, null, null, null, null, $ambito, null, null, null, 'es_ES.UTF-8', null, null,
            ['ini_dia' => 1, 'ini_mes' => 10, 'fin_dia' => 30, 'fin_mes' => 9],
            null,
        );
    }

    /**
     * @param list<string> $prefijosMapa
     */
    private function useCase(array $prefijosMapa = ['crb']): ActaSelectData
    {
        $delegaciones = $this->createStub(DelegacionRepositoryInterface::class);
        $delegaciones->method('getArrayDlRegionStgr')->willReturn([10 => 'dlb', 11 => 'dlmE']);
        $asignaturas = $this->createStub(AsignaturaRepositoryInterface::class);
        $asignaturas->method('getArrayAsignaturas')->willReturn([1101 => 'Philosophia I']);
        $mapa = $this->createStub(MapaPrefijoActaEsquemaRepositoryInterface::class);
        $mapa->method('prefijosPorEsquemaBase')->willReturn($prefijosMapa);

        return new ActaSelectData($delegaciones, $this->actas, $this->actasDl, $this->actasEx, $asignaturas, new ActaPrefijosDeEsquema($mapa));
    }

    /** @return list<string> */
    private function filtrosDe(string $repo): array
    {
        $this->assertCount(1, $this->consultas[$repo], "una consulta a $repo");

        return DescribeCriteria::filtros($this->consultas[$repo][0]);
    }

    public function test_dl_solo_numero_busca_con_todos_los_prefijos_propios_y_el_anyo_actual(): void
    {
        $this->useCase()->execute(['acta' => '12', 'titulo' => 'Buscar']);

        $y = date('y');
        $this->assertSame(["acta matches dlb 12/$y|crb 12/$y"], $this->filtrosDe('dl'));
        $this->assertSame([], $this->consultas['actas']);
    }

    public function test_dl_numero_y_anyo(): void
    {
        $this->useCase()->execute(['acta' => '12/24']);

        $this->assertSame(['acta matches dlb 12/24|crb 12/24'], $this->filtrosDe('dl'));
    }

    public function test_dl_sin_prefijos_usa_mi_dl(): void
    {
        $_SESSION['session_auth']['esquema'] = '';

        $this->useCase([])->execute(['acta' => '12/24']);

        $this->assertSame(['acta matches  12/24'], $this->filtrosDe('dl'), 'sin esquema en sesión no hay ningún prefijo');
    }

    public function test_region_del_stgr_busca_el_numero_en_todas_sus_dl(): void
    {
        $this->ambito('rstgr');

        $this->useCase()->execute(['acta' => '7']);

        $y = date('y');
        $this->assertSame(["acta matches dlb 7/$y|dlmE 7/$y"], $this->filtrosDe('actas'));
        $this->assertSame([], $this->consultas['dl']);

        $this->consultas['actas'] = [];
        $this->useCase()->execute(['acta' => '7/25']);
        $this->assertSame(['acta matches dlb 7/25|dlmE 7/25'], $this->filtrosDe('actas'));
    }

    public function test_dl_texto_busca_en_la_dl_y_si_no_hay_en_las_de_otras(): void
    {
        $out = $this->useCase()->execute(['acta' => 'crb', 'titulo' => 'Actas crb']);

        $this->assertSame(['acta matches crb'], $this->filtrosDe('dl'));
        $this->assertSame(['acta matches crb'], $this->filtrosDe('ex'));
        $this->assertSame(['f_acta desc', 'acta desc'], DescribeCriteria::orden($this->consultas['dl'][0]));
        $this->assertSame('Actas crb', $out['titulo']);
    }

    public function test_dl_texto_con_resultados_no_mira_las_de_otras(): void
    {
        $acta = $this->createStub(Acta::class);
        $acta->method('getActa')->willReturn('crb 3/24');
        $acta->method('getF_acta')->willReturn(new DateTimeLocal('2024-06-15'));
        $acta->method('getId_asignatura')->willReturn(1101);
        $acta->method('getPdf')->willReturn('%PDF');
        $this->resultadoDl = [$acta];

        $out = $this->useCase()->execute(['acta' => 'crb']);

        $this->assertSame([], $this->consultas['ex']);
        $this->assertSame('crb 3/24', $out['actas'][0]['acta']);
        $this->assertSame('15/6/2024', $out['actas'][0]['f_acta']);
        $this->assertSame(1101, $out['actas'][0]['id_asignatura']);
        $this->assertTrue($out['actas'][0]['has_pdf']);
        $this->assertSame([1101 => 'Philosophia I'], $out['a_asignaturas']);
    }

    public function test_region_del_stgr_texto(): void
    {
        $this->ambito('rstgr');

        $this->useCase()->execute(['acta' => 'dlb']);

        $this->assertSame(['acta matches dlb'], $this->filtrosDe('actas'));
    }

    public function test_sin_acta_lista_las_ultimas_20_del_curso(): void
    {
        $out = $this->useCase()->execute(['mes_fin_stgr' => 12]);

        $criteria = $this->consultas['dl'][0];
        $filtros = $this->filtrosDe('dl');
        $this->assertCount(1, $filtros);
        $this->assertStringStartsWith('f_acta between [', $filtros[0]);
        $this->assertSame(20, $criteria->getLimit());
        $this->assertSame(['f_acta desc', 'acta desc'], DescribeCriteria::orden($criteria));
        $this->assertStringContainsString('Máximo 20', $out['titulo']);
        $any = (int) date('Y');
        $this->assertStringContainsString(($any - 1) . '-10-01 - ' . $any . '-09-30', $out['titulo'], 'mes de fin 12: el curso que acaba este año');
    }

    public function test_sin_acta_pasado_el_mes_de_fin_es_el_curso_siguiente(): void
    {
        $out = $this->useCase()->execute(['mes_fin_stgr' => 0]);

        $any = (int) date('Y');
        $this->assertStringContainsString($any . '-10-01 - ' . ($any + 1) . '-09-30', $out['titulo']);
    }

    public function test_sin_acta_en_la_region_del_stgr_filtra_por_sus_dl(): void
    {
        $this->ambito('rstgr');

        $this->useCase()->execute([]);

        $filtros = $this->filtrosDe('actas');
        $this->assertStringStartsWith('f_acta between', $filtros[0]);
        $this->assertSame('acta matches ^(dlb |dlmE )', $filtros[1]);
    }
}
