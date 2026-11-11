<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\SacdAusenciasGetData;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use Tests\support\DescribeCriteria;

final class SacdAusenciasGetDataTest extends TestCase
{
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoSacdHorarioRepositoryInterface&MockObject $horarios;
    private EncargoSacdRepositoryInterface&MockObject $encargosSacd;
    private SacdAusenciasGetData $useCase;

    protected function setUp(): void
    {
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->horarios = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $this->encargosSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        $this->useCase = new SacdAusenciasGetData($this->encargos, $this->horarios, $this->encargosSacd);
    }

    private function encargo(int $idEnc, int $idTipo, string $desc): Encargo
    {
        $e = new Encargo();
        $e->setId_enc($idEnc);
        $e->setId_tipo_enc($idTipo);
        $e->setDesc_enc($desc);

        return $e;
    }

    private function encargoSacd(int $idItem, int $idEnc, ?string $fin = null): EncargoSacd
    {
        $o = new EncargoSacd();
        $o->setId_item($idItem);
        $o->setId_enc($idEnc);
        $o->setId_nom(10011);
        $o->setModoVo(2);
        $o->setF_ini(new DateTimeLocal('2025-03-15'));
        $o->setF_fin($fin === null ? null : new DateTimeLocal($fin));

        return $o;
    }

    private function horario(?string $diaRef, ?int $diaInc): EncargoSacdHorario
    {
        $h = new EncargoSacdHorario();
        $h->setId_item(1);
        $h->setId_enc(1);
        $h->setId_nom(10011);
        $h->setDia_ref($diaRef);
        $h->setDia_inc($diaInc);

        return $h;
    }

    /** Un encargo de ausencia ya asignado al sacd. */
    private function unaAusencia(int $idTipo = 7100): void
    {
        $this->encargos->method('matching')->willReturn([]);
        $this->encargosSacd->method('matching')->willReturn([$this->encargoSacd(900, 5)]);
        $this->encargos->method('findById')->willReturn($this->encargo(5, $idTipo, 'Vacaciones'));
    }

    public function test_los_tipos_de_ausencia_son_los_encargos_de_tipo_7_y_4_ordenados_por_tipo(): void
    {
        $this->encargos->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_tipo_enc matches (7|4)...'], DescribeCriteria::filtros($c));
            $this->assertSame(['id_tipo_enc asc'], DescribeCriteria::orden($c));

            return true;
        }))->willReturn([$this->encargo(5, 4100, 'Enfermedad'), $this->encargo(9, 7100, 'Vacaciones')]);
        $this->encargosSacd->method('matching')->willReturn([]);

        $out = $this->useCase->execute(10011, 0);

        $this->assertSame([5 => 'Enfermedad', 9 => 'Vacaciones'], $out['array_tipo_ausencias']);
    }

    public function test_sin_historial_solo_se_listan_las_que_empiezan_hoy_o_despues(): void
    {
        $this->encargos->method('matching')->willReturn([]);
        $this->encargosSacd->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $f = DescribeCriteria::filtros($c);
            $this->assertSame('id_nom eq 10011', $f[0]);
            $this->assertStringStartsWith('f_ini gte ' . date('Y-m-d'), $f[1]);
            $this->assertSame(['f_ini asc'], DescribeCriteria::orden($c));

            return true;
        }))->willReturn([]);

        $this->useCase->execute(10011, 0);
    }

    public function test_con_historial_se_listan_todas(): void
    {
        $this->encargos->method('matching')->willReturn([]);
        $this->encargosSacd->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_nom eq 10011'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->useCase->execute(10011, 1);
    }

    public function test_cada_fila_trae_el_encargo_y_las_fechas_en_formato_local(): void
    {
        $this->encargos->method('matching')->willReturn([]);
        $this->encargosSacd->method('matching')->willReturn([$this->encargoSacd(900, 5, '2025-03-20')]);
        $this->encargos->method('findById')->willReturn($this->encargo(5, 7100, 'Vacaciones'));
        $this->horarios->method('matching')->willReturn([]);

        $fila = $this->useCase->execute(10011, 1)['filas'][0];

        $this->assertSame(5, $fila['id_enc']);
        $this->assertSame(7100, $fila['id_tipo_enc']);
        $this->assertSame('Vacaciones', $fila['desc_enc']);
        $this->assertSame(900, $fila['id_item']);
        $this->assertSame((new DateTimeLocal('2025-03-15'))->getFromLocal(), $fila['inicio']);
        $this->assertSame((new DateTimeLocal('2025-03-20'))->getFromLocal(), $fila['fin']);
    }

    public function test_sin_fecha_de_fin_el_fin_es_null(): void
    {
        $this->unaAusencia();
        $this->horarios->method('matching')->willReturn([]);

        $this->assertNull($this->useCase->execute(10011, 1)['filas'][0]['fin']);
    }

    public function test_un_encargo_inexistente_se_omite(): void
    {
        $this->encargos->method('matching')->willReturn([]);
        $this->encargosSacd->method('matching')->willReturn([$this->encargoSacd(900, 5), $this->encargoSacd(901, 6)]);
        $this->encargos->method('findById')->willReturnCallback(fn (int $id) => $id === 6 ? $this->encargo(6, 7100, 'Otra') : null);
        $this->horarios->method('matching')->willReturn([]);

        $this->assertSame([901], array_column($this->useCase->execute(10011, 1)['filas'], 'id_item'));
    }

    public function test_los_encargos_que_no_son_de_ausencia_se_omiten(): void
    {
        $this->unaAusencia(2100);

        $this->assertSame([], $this->useCase->execute(10011, 1)['filas']);
    }

    /**
     * Hallazgo: el filtro de la fila (`preg_match('/[74]/')`) acepta un 7 o un 4 en cualquier posición,
     * mientras que la lista de tipos de ausencia usa el prefijo `(7|4)...`. Un tipo como 2040 se
     * mostraría como ausencia sin ser un tipo de ausencia.
     *
     * @dataProvider tiposProvider
     */
    public function test_filtro_de_tipo_de_la_fila(int $idTipo, bool $visible): void
    {
        $this->unaAusencia($idTipo);
        $this->horarios->method('matching')->willReturn([]);

        $this->assertCount($visible ? 1 : 0, $this->useCase->execute(10011, 1)['filas']);
    }

    /**
     * @return iterable<string, array{int, bool}>
     */
    public static function tiposProvider(): iterable
    {
        yield 'empieza por 7' => [7100, true];
        yield 'empieza por 4' => [4100, true];
        yield 'sin 7 ni 4' => [2100, false];
        yield 'contiene un 4 en medio (falso positivo)' => [2040, true];
        yield 'contiene un 7 al final (falso positivo)' => [1007, true];
    }

    public function test_la_dedicacion_por_modulo_sale_de_los_horarios_del_sacd(): void
    {
        $this->unaAusencia();
        $this->horarios->method('matching')->willReturn([$this->horario('m', 2), $this->horario('t', 3), $this->horario('v', 4), $this->horario('x', 9)]);

        $fila = $this->useCase->execute(10011, 1)['filas'][0];

        $this->assertSame(['2', '3', '4'], [$fila['dedic_m'], $fila['dedic_t'], $fila['dedic_v']]);
    }

    public function test_con_historial_se_cargan_todos_los_horarios_con_una_sola_consulta(): void
    {
        $this->unaAusencia();
        $this->horarios->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_enc eq 5', 'id_nom eq 10011'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->useCase->execute(10011, 1);
    }

    public function test_sin_historial_se_cargan_los_vigentes_y_los_futuros(): void
    {
        $this->encargos->method('matching')->willReturn([]);
        $this->encargosSacd->method('matching')->willReturn([$this->encargoSacd(900, 5)]);
        $this->encargos->method('findById')->willReturn($this->encargo(5, 7100, 'Vacaciones'));
        $this->horarios->expects($this->exactly(2))->method('matching')->willReturnCallback(
            fn (Criteria $c) => in_array('f_fin is_null', DescribeCriteria::filtros($c), true) ? [$this->horario('m', 1)] : [$this->horario('t', 2)]
        );

        $fila = $this->useCase->execute(10011, 0)['filas'][0];

        $this->assertSame('1', $fila['dedic_m']);
        $this->assertSame('2', $fila['dedic_t']);
    }

    public function test_el_token_para_guardar_va_firmado_con_el_sacd(): void
    {
        $this->encargos->method('matching')->willReturn([]);
        $this->encargosSacd->method('matching')->willReturn([]);

        $out = $this->useCase->execute(10011, 0);

        $this->assertSame(['id_nom' => 10011], HashB::open($out['ctx_guardar'], 'sacd_ausencias_update'));
        $this->assertSame([], $out['filas']);
    }
}
