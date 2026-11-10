<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\services\PropuestasHorarioTexto;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\shared\domain\criteria\Criteria;
use Tests\unit\planning\support\DescribeCriteria;

final class PropuestasHorarioTextoTest extends TestCase
{
    private EncargoSacdHorarioRepositoryInterface&MockObject $actual;
    private PropuestaEncargoSacdHorarioRepositoryInterface&MockObject $propuesta;
    private PropuestasHorarioTexto $servicio;

    protected function setUp(): void
    {
        $this->actual = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $this->propuesta = $this->createMock(PropuestaEncargoSacdHorarioRepositoryInterface::class);
        $this->servicio = new PropuestasHorarioTexto($this->actual, $this->propuesta);
    }

    private function horario(?string $diaRef, ?int $diaInc): EncargoSacdHorario
    {
        $h = new EncargoSacdHorario();
        $h->setId_item(1);
        $h->setId_enc(1);
        $h->setId_nom(1);
        $h->setDia_ref($diaRef);
        $h->setDia_inc($diaInc);

        return $h;
    }

    /**
     * @dataProvider idsInvalidosProvider
     */
    public function test_con_ids_no_positivos_el_texto_es_vacio_y_no_se_consulta(int $idEnc, int $idSacd): void
    {
        $this->actual->expects($this->never())->method('matching');
        $this->propuesta->expects($this->never())->method('matching');

        $this->assertSame('', $this->servicio->actualTxt($idEnc, $idSacd));
        $this->assertSame('', $this->servicio->propuestaTxt($idEnc, $idSacd));
    }

    /**
     * @return iterable<string, array{int, int}>
     */
    public static function idsInvalidosProvider(): iterable
    {
        yield 'encargo cero' => [0, 10011];
        yield 'sacd cero' => [7, 0];
        yield 'negativos' => [-1, -1];
    }

    public function test_el_texto_actual_resume_la_dedicacion_por_modulo(): void
    {
        $this->actual->method('matching')->willReturn([$this->horario('m', 2), $this->horario('t', 3), $this->horario('v', 4)]);

        $this->assertSame('m:[2]; t1:[3]; t2:[4]', $this->servicio->actualTxt(7, 10011));
    }

    public function test_sin_horarios_todos_los_modulos_son_cero(): void
    {
        $this->actual->method('matching')->willReturn([]);

        $this->assertSame('m:[0]; t1:[0]; t2:[0]', $this->servicio->actualTxt(7, 10011));
    }

    public function test_el_modulo_v_se_etiqueta_como_t2(): void
    {
        $this->actual->method('matching')->willReturn([$this->horario('v', 5)]);

        $this->assertSame('m:[0]; t1:[0]; t2:[5]', $this->servicio->actualTxt(7, 10011));
    }

    public function test_modulos_desconocidos_o_sin_dedicacion_se_ignoran(): void
    {
        $this->actual->method('matching')->willReturn([$this->horario('x', 9), $this->horario(null, 9), $this->horario('m', null)]);

        $this->assertSame('m:[0]; t1:[0]; t2:[0]', $this->servicio->actualTxt(7, 10011));
    }

    public function test_si_hay_varios_horarios_del_mismo_modulo_gana_el_ultimo(): void
    {
        $this->actual->method('matching')->willReturn([$this->horario('m', 1), $this->horario('m', 6)]);

        $this->assertSame('m:[6]; t1:[0]; t2:[0]', $this->servicio->actualTxt(7, 10011));
    }

    public function test_el_actual_consulta_solo_los_horarios_vigentes_del_sacd_en_el_encargo(): void
    {
        $this->actual->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_enc eq 7', 'id_nom eq 10011', 'f_fin is_null'], DescribeCriteria::filtros($c));
            $this->assertSame(['f_ini desc'], DescribeCriteria::orden($c));

            return true;
        }))->willReturn([]);
        $this->propuesta->expects($this->never())->method('matching');

        $this->servicio->actualTxt(7, 10011);
    }

    public function test_la_propuesta_sale_de_su_propio_repositorio(): void
    {
        $this->propuesta->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_enc eq 7', 'id_nom eq 10011', 'f_fin is_null'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([$this->horario('t', 8)]);
        $this->actual->expects($this->never())->method('matching');

        $this->assertSame('m:[0]; t1:[8]; t2:[0]', $this->servicio->propuestaTxt(7, 10011));
    }
}
