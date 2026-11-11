<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoSacdHorarioVerData;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\EncargoConstants;
use src\encargossacd\domain\services\EncargoDominioService;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\shared\security\HashB;
use Tests\support\DescribeCriteria;

final class EncargoSacdHorarioVerDataTest extends TestCase
{
    private EncargoDominioService&MockObject $dominio;
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoSacdHorarioRepositoryInterface&MockObject $horarios;
    private EncargoSacdRepositoryInterface&MockObject $encargosSacd;
    private PersonaDlRepositoryInterface&MockObject $personas;
    private EncargoSacdHorarioVerData $useCase;

    protected function setUp(): void
    {
        $this->dominio = $this->createMock(EncargoDominioService::class);
        $this->dominio->method('calcular_dia')->willReturn('');
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->horarios = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $this->encargosSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        $this->personas = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->useCase = new EncargoSacdHorarioVerData($this->dominio, $this->encargos, $this->horarios, $this->encargosSacd, $this->personas);
    }

    private function persona(string $nombre): PersonaDl
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getPrefApellidosNombre')->willReturn($nombre);

        return $p;
    }

    private function encargo(string $desc): Encargo
    {
        $e = new Encargo();
        $e->setId_enc(7);
        $e->setDesc_enc($desc);

        return $e;
    }

    private function horario(int $idItem = 55): EncargoSacdHorario
    {
        $h = new EncargoSacdHorario();
        $h->setId_item($idItem);
        $h->setId_enc(7);
        $h->setId_nom(10011);
        $h->setF_ini(new DateTimeLocal('2025-03-15'));
        $h->setF_fin(new DateTimeLocal('2025-12-31'));
        $h->setDia_ref('v');
        $h->setDia_num(2);
        $h->setMas_menos('+');
        $h->setDia_inc(3);
        $h->setH_ini(TimeLocal::fromString('09:30:00'));
        $h->setH_fin(TimeLocal::fromString('10:45:00'));

        return $h;
    }

    // ---- horario existente --------------------------------------------------------------------------

    public function test_con_item_indicado_muestra_el_horario_existente(): void
    {
        $this->personas->method('findById')->with(10011)->willReturn($this->persona('Perez, Ana'));
        $this->horarios->expects($this->once())->method('findById')->with(55)->willReturn($this->horario());
        $this->horarios->method('countExcepcionesByHorarioId')->with(55)->willReturn(2);
        $this->encargos->method('findById')->willReturn($this->encargo('Misa diaria'));

        $out = $this->useCase->cargar(10011, 7, 55, '');

        $this->assertSame('Perez, Ana', $out['ap_nom']);
        $this->assertStringEndsWith(': Misa diaria', $out['titulo']);
        $this->assertSame(55, $out['id_item']);
        $this->assertSame('Misa diaria', $out['desc_enc']);
        $this->assertSame((new DateTimeLocal('2025-03-15'))->getFromLocal(), $out['f_ini_iso']);
        $this->assertSame((new DateTimeLocal('2025-12-31'))->getFromLocal(), $out['f_fin_iso']);
        $this->assertTrue($out['tiene_excepciones']);
        $this->assertSame(['v', '2', '+', '3', '09:30:00', '10:45:00'], [$out['dia_ref'], $out['dia_num'], $out['mas_menos'], $out['dia_inc'], $out['h_ini'], $out['h_fin']]);
    }

    public function test_sin_excepciones_no_se_marca(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->horarios->method('findById')->willReturn($this->horario());
        $this->horarios->method('countExcepcionesByHorarioId')->willReturn(0);
        $this->encargos->method('findById')->willReturn(null);

        $this->assertFalse($this->useCase->cargar(10011, 7, 55, 'desc')['tiene_excepciones']);
    }

    public function test_la_descripcion_recibida_manda_sobre_la_del_encargo_pero_el_titulo_usa_la_del_encargo(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->horarios->method('findById')->willReturn($this->horario());
        $this->horarios->method('countExcepcionesByHorarioId')->willReturn(0);
        $this->encargos->method('findById')->willReturn($this->encargo('Del encargo'));

        $out = $this->useCase->cargar(10011, 7, 55, 'Recibida');

        $this->assertSame('Recibida', $out['desc_enc']);
        $this->assertStringEndsWith(': Del encargo', $out['titulo']);
    }

    public function test_si_el_encargo_no_existe_se_usa_la_descripcion_recibida(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->horarios->method('findById')->willReturn($this->horario());
        $this->horarios->method('countExcepcionesByHorarioId')->willReturn(0);
        $this->encargos->method('findById')->willReturn(null);

        $out = $this->useCase->cargar(10011, 7, 55, 'Recibida');

        $this->assertStringEndsWith(': Recibida', $out['titulo']);
    }

    public function test_una_persona_inexistente_deja_el_nombre_vacio(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->horarios->method('findById')->willReturn(null);
        $this->encargosSacd->method('matching')->willReturn([]);
        $this->encargos->method('findById')->willReturn(null);

        $this->assertSame('', $this->useCase->cargar(10011, 7, 0, '')['ap_nom']);
    }

    public function test_los_tokens_de_un_horario_existente(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->horarios->method('findById')->willReturn($this->horario());
        $this->horarios->method('countExcepcionesByHorarioId')->willReturn(0);
        $this->encargos->method('findById')->willReturn(null);

        $out = $this->useCase->cargar(10011, 7, 55, 'x');

        $this->assertSame(['id_nom' => 10011, 'id_enc' => 7, 'id_item' => 55], HashB::open($out['ctx_guardar'], 'horario_sacd_update_data'));
        $this->assertSame(['id_item' => 55], HashB::open($out['ctx_eliminar'], 'horario_sacd_update_data'));
    }

    // ---- búsqueda del horario más reciente ---------------------------------------------------------------

    public function test_sin_item_busca_el_horario_mas_reciente_del_sacd_en_el_encargo(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->horarios->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_nom eq 10011', 'id_enc eq 7'], DescribeCriteria::filtros($c));
            $this->assertSame(['id_item desc'], DescribeCriteria::orden($c));
            $this->assertSame(1, $c->getLimit());

            return true;
        }))->willReturn([$this->horario(77)]);
        $this->horarios->expects($this->once())->method('findById')->with(77)->willReturn($this->horario(77));
        $this->horarios->method('countExcepcionesByHorarioId')->willReturn(0);
        $this->encargos->method('findById')->willReturn(null);

        $this->assertSame(77, $this->useCase->cargar(10011, 7, 0, '')['id_item']);
    }

    public function test_un_item_negativo_se_trata_como_sin_item(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->horarios->expects($this->once())->method('matching')->willReturn([]);
        $this->encargosSacd->method('matching')->willReturn([]);
        $this->encargos->method('findById')->willReturn(null);

        $this->assertSame(0, $this->useCase->cargar(10011, 7, -5, '')['id_item']);
    }

    // ---- horario nuevo -------------------------------------------------------------------------------------------

    public function test_sin_horario_ofrece_un_formulario_vacio_con_las_fechas_del_encargo_del_sacd(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->horarios->method('matching')->willReturn([]);
        $sacd = new EncargoSacd();
        $sacd->setId_item(1);
        $sacd->setId_enc(7);
        $sacd->setId_nom(10011);
        $sacd->setModoVo(3);
        $sacd->setF_ini(new DateTimeLocal('2024-09-01'));
        $this->encargosSacd->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_nom eq 10011', 'id_enc eq 7'], DescribeCriteria::filtros($c));
            $this->assertSame(1, $c->getLimit());

            return true;
        }))->willReturn([$sacd]);
        $this->encargos->method('findById')->willReturn($this->encargo('Misa diaria'));

        $out = $this->useCase->cargar(10011, 7, 0, '');

        $this->assertSame(0, $out['id_item']);
        $this->assertSame((new DateTimeLocal('2024-09-01'))->getFromLocal(), $out['f_ini_iso']);
        $this->assertSame('', $out['f_fin_iso']);
        $this->assertFalse($out['tiene_excepciones']);
        foreach (['dia_ref', 'dia_num', 'mas_menos', 'dia_inc', 'h_ini', 'h_fin'] as $campo) {
            $this->assertSame('', $out[$campo], $campo);
        }
        $this->assertSame('Misa diaria', $out['encabezado_desc']);
        $this->assertStringEndsWith(': Misa diaria', $out['titulo']);
    }

    public function test_si_el_item_indicado_no_existe_cae_al_formulario_nuevo(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->horarios->method('findById')->with(55)->willReturn(null);
        $this->encargosSacd->method('matching')->willReturn([]);
        $this->encargos->method('findById')->willReturn(null);

        $out = $this->useCase->cargar(10011, 7, 55, 'Recibida');

        $this->assertSame(0, $out['id_item']);
        $this->assertSame('', $out['ctx_eliminar'], 'sin horario no hay token de eliminar');
        $this->assertSame(['id_nom' => 10011, 'id_enc' => 7, 'id_item' => 0], HashB::open($out['ctx_guardar'], 'horario_sacd_update_data'));
    }

    public function test_en_el_formulario_nuevo_la_descripcion_recibida_manda_en_el_titulo_y_la_del_encargo_en_el_encabezado(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->horarios->method('matching')->willReturn([]);
        $this->encargosSacd->method('matching')->willReturn([]);
        $this->encargos->method('findById')->willReturn($this->encargo('Del encargo'));

        $out = $this->useCase->cargar(10011, 7, 0, 'Recibida');

        $this->assertStringEndsWith(': Recibida', $out['titulo']);
        $this->assertSame('Recibida', $out['desc_enc']);
        $this->assertSame('Del encargo', $out['encabezado_desc']);
    }

    public function test_sin_encargo_el_encabezado_usa_la_descripcion_recibida(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->horarios->method('matching')->willReturn([]);
        $this->encargosSacd->method('matching')->willReturn([]);
        $this->encargos->method('findById')->willReturn(null);

        $this->assertSame('Recibida', $this->useCase->cargar(10011, 7, 0, 'Recibida')['encabezado_desc']);
    }

    public function test_incluye_las_opciones_de_los_desplegables_y_el_dia_calculado(): void
    {
        $dominio = $this->createMock(EncargoDominioService::class);
        $dominio->expects($this->once())->method('calcular_dia')->with('+', 'v', '3')->willReturn(5);
        $uc = new EncargoSacdHorarioVerData($dominio, $this->encargos, $this->horarios, $this->encargosSacd, $this->personas);
        $this->personas->method('findById')->willReturn(null);
        $this->horarios->method('findById')->willReturn($this->horario());
        $this->horarios->method('countExcepcionesByHorarioId')->willReturn(0);
        $this->encargos->method('findById')->willReturn(null);

        $out = $uc->cargar(10011, 7, 55, 'x');

        $this->assertSame('5', $out['dia']);
        $this->assertSame(EncargoConstants::OPCIONES_DIA_SEMANA, $out['opciones_dia_semana']);
        $this->assertSame(EncargoConstants::OPCIONES_DIA_REF, $out['opciones_dia_ref']);
        $this->assertSame(EncargoConstants::OPCIONES_ORDINALES, $out['opciones_ordinales']);
    }
}
