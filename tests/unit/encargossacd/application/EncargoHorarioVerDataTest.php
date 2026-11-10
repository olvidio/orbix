<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoHorarioVerData;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoHorario;
use src\encargossacd\domain\EncargoConstants;
use src\encargossacd\domain\services\EncargoDominioService;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\shared\security\HashB;

final class EncargoHorarioVerDataTest extends TestCase
{
    private EncargoDominioService&MockObject $dominio;
    private EncargoHorarioRepositoryInterface&MockObject $repo;
    private EncargoHorarioVerData $useCase;

    protected function setUp(): void
    {
        $this->dominio = $this->createMock(EncargoDominioService::class);
        $this->repo = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $this->useCase = new EncargoHorarioVerData($this->dominio, $this->repo);
    }

    private function horario(): EncargoHorario
    {
        $h = new EncargoHorario();
        $h->setId_item_h(55);
        $h->setId_enc(7);
        $h->setF_ini(new DateTimeLocal('2025-03-15'));
        $h->setF_fin(new DateTimeLocal('2025-12-31'));
        $h->setDia_ref('v');
        $h->setDia_num(2);
        $h->setMas_menos('+');
        $h->setDia_inc(3);
        $h->setH_ini(TimeLocal::fromString('09:30:00'));
        $h->setH_fin(TimeLocal::fromString('10:45:00'));
        $h->setN_sacd(2);
        $h->setMes(4);

        return $h;
    }

    public function test_nuevo_devuelve_el_formulario_vacio_sin_consultar_el_repositorio(): void
    {
        $this->repo->expects($this->never())->method('findById');
        $this->dominio->method('calcular_dia')->willReturn('');

        $out = $this->useCase->cargar('nuevo', 7, 55);

        foreach (['id_item_h', 'f_ini', 'f_fin', 'dia_ref', 'dia_num', 'mas_menos', 'dia_inc', 'h_ini', 'h_fin', 'n_sacd', 'mes'] as $campo) {
            $this->assertSame('', $out[$campo], $campo);
        }
    }

    public function test_sin_id_valido_tambien_es_nuevo(): void
    {
        $this->repo->expects($this->never())->method('findById');
        $this->dominio->method('calcular_dia')->willReturn('');

        $this->assertSame('', $this->useCase->cargar('editar', 7, 0)['id_item_h']);
        $this->assertSame('', $this->useCase->cargar('editar', 7, -3)['id_item_h']);
    }

    public function test_editar_serializa_el_horario_existente(): void
    {
        $this->repo->expects($this->once())->method('findById')->with(55)->willReturn($this->horario());
        $this->dominio->method('calcular_dia')->willReturn('');

        $out = $this->useCase->cargar('editar', 7, 55);

        $this->assertSame('55', $out['id_item_h']);
        $this->assertSame((new DateTimeLocal('2025-03-15'))->getFromLocal(), $out['f_ini']);
        $this->assertSame((new DateTimeLocal('2025-12-31'))->getFromLocal(), $out['f_fin']);
        $this->assertSame('v', $out['dia_ref']);
        $this->assertSame('2', $out['dia_num']);
        $this->assertSame('+', $out['mas_menos']);
        $this->assertSame('3', $out['dia_inc']);
        $this->assertSame('09:30:00', $out['h_ini']);
        $this->assertSame('10:45:00', $out['h_fin']);
        $this->assertSame('2', $out['n_sacd']);
        $this->assertSame('4', $out['mes']);
    }

    public function test_un_horario_inexistente_cae_al_formulario_nuevo(): void
    {
        $this->repo->method('findById')->willReturn(null);
        $this->dominio->method('calcular_dia')->willReturn('');

        $out = $this->useCase->cargar('editar', 7, 55);

        $this->assertSame('', $out['id_item_h']);
        $this->assertSame(['id_enc' => 7, 'id_item_h' => 0], HashB::open($out['ctx_guardar'], 'horario_update_data'));
    }

    public function test_el_dia_lo_calcula_el_dominio_con_mas_menos_dia_ref_y_dia_inc(): void
    {
        $this->repo->method('findById')->willReturn($this->horario());
        $this->dominio->expects($this->once())->method('calcular_dia')->with('+', 'v', '3')->willReturn(5);

        $this->assertSame('5', $this->useCase->cargar('editar', 7, 55)['dia']);
    }

    public function test_incluye_las_opciones_de_los_desplegables(): void
    {
        $this->dominio->method('calcular_dia')->willReturn('');

        $out = $this->useCase->cargar('nuevo', 7, 0);

        $this->assertSame(EncargoConstants::OPCIONES_DIA_SEMANA, $out['opciones_dia_semana']);
        $this->assertSame(EncargoConstants::OPCIONES_DIA_REF, $out['opciones_dia_ref']);
        $this->assertSame(EncargoConstants::OPCIONES_ORDINALES, $out['opciones_ordinales']);
    }

    public function test_el_token_para_guardar_va_firmado_con_el_encargo_y_el_horario(): void
    {
        $this->repo->method('findById')->willReturn($this->horario());
        $this->dominio->method('calcular_dia')->willReturn('');

        $out = $this->useCase->cargar('editar', 7, 55);

        $this->assertSame(['id_enc' => 7, 'id_item_h' => 55], HashB::open($out['ctx_guardar'], 'horario_update_data'));
    }

    public function test_un_horario_sin_opcionales_los_muestra_vacios(): void
    {
        $h = new EncargoHorario();
        $h->setId_item_h(60);
        $h->setId_enc(7);
        $h->setF_ini(new DateTimeLocal('2025-03-15'));
        $this->repo->method('findById')->willReturn($h);
        $this->dominio->method('calcular_dia')->willReturn('');

        $out = $this->useCase->cargar('editar', 7, 60);

        foreach (['f_fin', 'dia_ref', 'dia_num', 'mas_menos', 'dia_inc', 'h_ini', 'h_fin', 'n_sacd', 'mes'] as $campo) {
            $this->assertSame('', $out[$campo], $campo);
        }
    }
}
