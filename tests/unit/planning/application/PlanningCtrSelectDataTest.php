<?php

declare(strict_types=1);

namespace Tests\unit\planning\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\planning\application\ActividadesDePersonaService;
use src\planning\application\PlanningCtrSelectData;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use Tests\unit\planning\support\DescribeCriteria;

final class PlanningCtrSelectDataTest extends TestCase
{
    private PersonaDlRepositoryInterface&MockObject $personas;
    private CentroDlRepositoryInterface&MockObject $centros;
    private ActividadesDePersonaService&MockObject $actividades;
    private PlanningCtrSelectData $useCase;
    private DateTimeLocal $ini;

    protected function setUp(): void
    {
        $this->personas = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->centros = $this->createMock(CentroDlRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadesDePersonaService::class);
        $this->useCase = new PlanningCtrSelectData($this->personas, $this->centros, $this->actividades);
        $this->ini = new DateTimeLocal('2025-01-01');
    }

    private function centro(int $id, string $nombre): CentroDl
    {
        $c = $this->createMock(CentroDl::class);
        $c->method('getId_ubi')->willReturn($id);
        $c->method('getNombre_ubi')->willReturn($nombre);

        return $c;
    }

    private function persona(): PersonaDl
    {
        return $this->createMock(PersonaDl::class);
    }

    /**
     * @param array<string, mixed> $input
     * @return array{msg_txt: string, cabecera_title: string, a_actividades2: array<int|string, mixed>}
     */
    private function ejecutar(array $input): array
    {
        return $this->useCase->execute($input, $this->ini, '1/1/2025', '2025-03-31', '2025-01-01');
    }

    // ---- sin centro: todas las personas de una tabla -------------------------------------

    public function test_sin_ctr_toma_los_numerarios_activos_no_sacd_ordenados(): void
    {
        $this->personas->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['situacion eq A', 'sacd eq false', 'id_tabla eq n'], DescribeCriteria::filtros($c));
            $this->assertSame(['id_ctr asc', 'apellido1 asc'], DescribeCriteria::orden($c));

            return true;
        }))->willReturn([]);

        $out = $this->ejecutar([]);

        $this->assertSame('Centros', $out['cabecera_title']);
        $this->assertSame('', $out['msg_txt']);
    }

    /**
     * @dataProvider tablasProvider
     * @param array<string, string> $input
     */
    public function test_sin_ctr_la_tabla_depende_de_los_marcadores(array $input, string $idTabla): void
    {
        $this->personas->method('matching')->with($this->callback(function (Criteria $c) use ($idTabla): bool {
            $this->assertContains("id_tabla eq $idTabla", DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->ejecutar($input);
    }

    /**
     * @return iterable<string, array{array<string, string>, string}>
     */
    public static function tablasProvider(): iterable
    {
        yield 'por defecto: numerarios' => [[], 'n'];
        yield 'todos_n no cambia' => [['todos_n' => '1'], 'n'];
        yield 'agregados' => [['todos_agd' => '1'], 'a'];
        yield 'supernumerarios' => [['todos_s' => '1'], 's'];
        yield 'si vienen agd y s, gana s' => [['todos_agd' => '1', 'todos_s' => '1'], 's'];
    }

    public function test_con_sacd_no_se_excluye_a_los_sacd(): void
    {
        $this->personas->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['situacion eq A', 'id_tabla eq n'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->ejecutar(['sacd' => '1']);
    }

    // ---- con centro -----------------------------------------------------------------------------

    public function test_centro_inexistente_avisa_y_no_busca_personas(): void
    {
        $this->centros->method('matching')->willReturn([]);
        $this->personas->expects($this->never())->method('matching');
        $this->actividades->expects($this->once())->method('actividadesPorPersona')
            ->with([], '2025-03-31', '2025-01-01', $this->ini, '1/1/2025', true)->willReturn([]);

        $out = $this->ejecutar(['ctr' => 'zzz']);

        $this->assertSame('No encuentro este ctr', $out['msg_txt']);
        $this->assertSame('', $out['cabecera_title']);
        $this->assertSame([], $out['a_actividades2']);
    }

    public function test_busca_el_centro_por_nombre_sin_acentos_con_inicio_anclado_y_escapa_el_mas(): void
    {
        $this->centros->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['nombre_ubi matches_unaccented ^Cent\\+ro'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->ejecutar(['ctr' => 'Cent+ro']);
    }

    public function test_con_centro_busca_sus_personas_ordenadas_por_apellido(): void
    {
        $this->centros->method('matching')->willReturn([$this->centro(42, 'Centro Norte')]);
        $p = $this->persona();
        $this->personas->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['situacion eq A', 'sacd eq false', 'id_ctr eq 42'], DescribeCriteria::filtros($c));
            $this->assertSame(['apellido1 asc'], DescribeCriteria::orden($c));

            return true;
        }))->willReturn([$p]);
        $this->actividades->expects($this->once())->method('actividadesPorPersona')
            ->with([$p], '2025-03-31', '2025-01-01', $this->ini, '1/1/2025', true)
            ->willReturn(['Centro Norte' => ['x']]);

        $out = $this->ejecutar(['ctr' => 'Centro']);

        $this->assertSame('Personas de: Centro Norte', $out['cabecera_title']);
        $this->assertSame('', $out['msg_txt']);
        $this->assertSame(['Centro Norte' => ['x']], $out['a_actividades2']);
    }

    public function test_centro_sin_personas_avisa_con_su_nombre(): void
    {
        $this->centros->method('matching')->willReturn([$this->centro(7, 'Centro Vacío')]);
        $this->personas->method('matching')->willReturn([]);
        $this->actividades->method('actividadesPorPersona')->willReturn([]);

        $out = $this->ejecutar(['ctr' => 'Centro']);

        $this->assertSame('No encuentro personas para Centro Vacío<br>', $out['msg_txt']);
    }

    public function test_varios_centros_juntan_las_personas_y_avisan_solo_de_los_vacios(): void
    {
        $this->centros->method('matching')->willReturn([
            $this->centro(1, 'Con gente'),
            $this->centro(2, 'Vacío'),
            $this->centro(3, 'Con gente 2'),
        ]);
        $a = $this->persona();
        $b = $this->persona();
        $this->personas->method('matching')->willReturnOnConsecutiveCalls([$a], [], [$b]);
        $this->actividades->expects($this->once())->method('actividadesPorPersona')
            ->with([$a, $b], $this->anything(), $this->anything(), $this->anything(), $this->anything(), true)
            ->willReturn([]);

        $out = $this->ejecutar(['ctr' => 'Con']);

        $this->assertSame('No encuentro personas para Vacío<br>', $out['msg_txt']);
        $this->assertSame('Personas de: Con gente 2', $out['cabecera_title'], 'la cabecera es la del último centro');
    }

    public function test_ctr_vacio_equivale_a_no_indicarlo(): void
    {
        $this->centros->expects($this->never())->method('matching');
        $this->personas->expects($this->once())->method('matching')->willReturn([]);

        $this->assertSame('Centros', $this->ejecutar(['ctr' => ''])['cabecera_title']);
    }
}
