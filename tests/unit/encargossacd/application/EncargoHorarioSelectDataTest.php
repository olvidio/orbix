<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoHorarioSelectData;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoHorario;
use src\encargossacd\domain\services\EncargoDominioService;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\shared\security\HashB;
use Tests\unit\planning\support\DescribeCriteria;

final class EncargoHorarioSelectDataTest extends TestCase
{
    private EncargoDominioService&MockObject $dominio;
    private EncargoHorarioRepositoryInterface&MockObject $horarios;
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoHorarioSelectData $useCase;

    protected function setUp(): void
    {
        $this->dominio = $this->createMock(EncargoDominioService::class);
        $this->horarios = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->useCase = new EncargoHorarioSelectData($this->dominio, $this->horarios, $this->encargos);
    }

    private function horario(int $idItem): EncargoHorario
    {
        $h = new EncargoHorario();
        $h->setId_item_h($idItem);
        $h->setId_enc(7);
        $h->setDia_ref('v');
        $h->setDia_num(2);
        $h->setMas_menos('+');
        $h->setDia_inc(3);
        $h->setH_ini(TimeLocal::fromString('09:30:00'));
        $h->setH_fin(TimeLocal::fromString('10:45:00'));
        $h->setN_sacd(2);
        $h->setMes(4);
        $h->setF_ini(new DateTimeLocal('2025-03-15'));

        return $h;
    }

    public function test_busca_los_horarios_del_encargo(): void
    {
        $this->horarios->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_enc eq 7'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->useCase->execute(7);
    }

    public function test_incluye_la_descripcion_del_encargo(): void
    {
        $e = new Encargo();
        $e->setDesc_enc('Misa diaria');
        $this->encargos->method('findByID')->with(7)->willReturn($e);
        $this->horarios->method('matching')->willReturn([]);

        $out = $this->useCase->execute(7);

        $this->assertSame('Misa diaria', $out['desc_enc']);
        $this->assertSame([], $out['filas']);
    }

    public function test_un_encargo_inexistente_deja_la_descripcion_vacia_y_no_falla(): void
    {
        $this->encargos->method('findByID')->willReturn(null);
        $this->horarios->method('matching')->willReturn([$this->horario(5)]);
        $this->dominio->method('texto_horario')->willReturn('');

        $out = $this->useCase->execute(7);

        $this->assertSame('', $out['desc_enc']);
        $this->assertCount(1, $out['filas']);
    }

    /**
     * Regresión: TimeLocal no tiene value() ni __toString(); las horas llegaban vacías al texto del horario.
     */
    public function test_las_horas_llegan_al_texto_del_horario(): void
    {
        $this->encargos->method('findByID')->willReturn(null);
        $this->horarios->method('matching')->willReturn([$this->horario(5)]);
        $this->dominio->expects($this->once())->method('texto_horario')
            ->with($this->anything(), $this->anything(), $this->anything(), $this->anything(), '09:30:00', '10:45:00', $this->anything())
            ->willReturn('');

        $this->useCase->execute(7);
    }

    public function test_cada_fila_lleva_los_campos_en_texto_y_el_texto_calculado_por_el_dominio(): void
    {
        $this->encargos->method('findByID')->willReturn(null);
        $this->horarios->method('matching')->willReturn([$this->horario(5)]);
        $this->dominio->expects($this->once())->method('texto_horario')
            ->with('+', 'v', '3', '2', '09:30:00', '10:45:00', '2')
            ->willReturn('viernes después del segundo domingo');

        $fila = $this->useCase->execute(7)['filas'][0];

        $this->assertSame(7, $fila['id_enc']);
        $this->assertSame(5, $fila['id_item_h']);
        $this->assertSame('2', $fila['dia_num']);
        $this->assertSame('v', $fila['dia_ref']);
        $this->assertSame('+', $fila['mas_menos']);
        $this->assertSame('3', $fila['dia_inc']);
        $this->assertSame('09:30:00', $fila['h_ini']);
        $this->assertSame('10:45:00', $fila['h_fin']);
        $this->assertSame('2', $fila['n_sacd']);
        $this->assertSame('4', $fila['mes']);
        $this->assertSame('', $fila['excep']);
        $this->assertSame('viernes después del segundo domingo', $fila['texto_horario']);
        $this->assertNotNull($fila['f_ini']);
        $this->assertNull($fila['f_fin'], 'sin fecha fin');
    }

    public function test_los_campos_nulos_salen_como_cadena_vacia(): void
    {
        $h = new EncargoHorario();
        $h->setId_item_h(9);
        $h->setId_enc(7);
        $h->setF_ini(new DateTimeLocal('2025-03-15')); // única fecha obligatoria
        $this->encargos->method('findByID')->willReturn(null);
        $this->horarios->method('matching')->willReturn([$h]);
        $this->dominio->expects($this->once())->method('texto_horario')->with('', '', '', '', '', '', '')->willReturn('');

        $fila = $this->useCase->execute(7)['filas'][0];

        foreach (['dia_num', 'dia_ref', 'mas_menos', 'dia_inc', 'h_ini', 'h_fin', 'n_sacd', 'mes'] as $campo) {
            $this->assertSame('', $fila[$campo], $campo);
        }
        $this->assertNull($fila['f_fin']);
    }

    public function test_el_token_de_eliminar_se_firma_con_el_id_del_horario(): void
    {
        $this->encargos->method('findByID')->willReturn(null);
        $this->horarios->method('matching')->willReturn([$this->horario(5), $this->horario(6)]);
        $this->dominio->method('texto_horario')->willReturn('');

        $filas = $this->useCase->execute(7)['filas'];

        $this->assertSame(['id_item_h' => 5], HashB::open($filas[0]['ctx_eliminar'], 'horario_update_data'));
        $this->assertSame(['id_item_h' => 6], HashB::open($filas[1]['ctx_eliminar'], 'horario_update_data'));
        $this->assertNotSame($filas[0]['ctx_eliminar'], $filas[1]['ctx_eliminar']);
    }

    public function test_conserva_el_orden_del_repositorio(): void
    {
        $this->encargos->method('findByID')->willReturn(null);
        $this->horarios->method('matching')->willReturn([$this->horario(30), $this->horario(4), $this->horario(17)]);
        $this->dominio->method('texto_horario')->willReturn('');

        $ids = array_column($this->useCase->execute(7)['filas'], 'id_item_h');

        $this->assertSame([30, 4, 17], $ids);
    }
}
