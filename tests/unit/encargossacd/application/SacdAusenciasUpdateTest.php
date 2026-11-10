<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\SacdAusenciasUpdate;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\shared\domain\value_objects\DateTimeLocal;

final class SacdAusenciasUpdateTest extends TestCase
{
    private EncargoSacdHorarioRepositoryInterface&MockObject $horarios;
    private EncargoSacdRepositoryInterface&MockObject $encargosSacd;
    private SacdAusenciasUpdate $useCase;

    protected function setUp(): void
    {
        $this->horarios = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $this->encargosSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        $this->useCase = new SacdAusenciasUpdate($this->horarios, $this->encargosSacd);
    }

    private function local(string $iso): string
    {
        return (new DateTimeLocal($iso))->getFromLocal();
    }

    /** @return array<string, mixed> */
    private function data(array $extra = []): array
    {
        return array_merge([
            'enc_num' => '1', 'id_nom' => '10011',
            'inicio' => [$this->local('2025-03-15')], 'fin' => [$this->local('2025-03-20')],
            'id_enc' => ['5'], 'id_item' => ['0'],
        ], $extra);
    }

    private function sacd(int $idItem = 900): EncargoSacd
    {
        $o = new EncargoSacd();
        $o->setId_item($idItem);
        $o->setId_enc(5);
        $o->setId_nom(10011);
        $o->setModoVo(2);
        $o->setF_ini(new DateTimeLocal('2025-01-01'));

        return $o;
    }

    // ---- alta (id_item = 0) ----------------------------------------------------------------------

    public function test_una_fila_nueva_crea_el_encargo_del_sacd_en_modo_2_y_su_horario(): void
    {
        $this->encargosSacd->method('getNewId')->willReturn(900);
        $this->horarios->method('getNewId')->willReturn(800);
        $this->horarios->method('findById')->with(900)->willReturn(null);
        $guardadoSacd = null;
        $guardadoHorario = null;
        $this->encargosSacd->expects($this->once())->method('Guardar')->willReturnCallback(function (EncargoSacd $o) use (&$guardadoSacd): bool {
            $guardadoSacd = $o;

            return true;
        });
        $this->horarios->expects($this->once())->method('Guardar')->willReturnCallback(function (EncargoSacdHorario $h) use (&$guardadoHorario): bool {
            $guardadoHorario = $h;

            return true;
        });

        $out = $this->useCase->execute($this->data());

        $this->assertSame(['error' => false, 'mensajes' => ''], $out);
        $this->assertSame(900, $guardadoSacd->getId_item());
        $this->assertSame(5, $guardadoSacd->getId_enc());
        $this->assertSame(10011, $guardadoSacd->getId_nom());
        $this->assertSame(2, $guardadoSacd->getModo());
        $this->assertSame('2025-03-15', $guardadoSacd->getF_ini()->format('Y-m-d'));
        $this->assertSame('2025-03-20', $guardadoSacd->getF_fin()->format('Y-m-d'));
        $this->assertSame(800, $guardadoHorario->getId_item());
        $this->assertSame(900, $guardadoHorario->getId_item_tarea_sacd());
        $this->assertSame(5, $guardadoHorario->getId_enc());
        $this->assertSame(10011, $guardadoHorario->getId_nom());
        $this->assertSame('2025-03-20', $guardadoHorario->getF_fin()->format('Y-m-d'));
    }

    public function test_sin_fecha_de_fin_el_fin_es_el_inicio(): void
    {
        $this->encargosSacd->method('getNewId')->willReturn(900);
        $this->horarios->method('getNewId')->willReturn(800);
        $guardado = null;
        $this->encargosSacd->method('Guardar')->willReturnCallback(function (EncargoSacd $o) use (&$guardado): bool {
            $guardado = $o;

            return true;
        });
        $this->horarios->method('Guardar')->willReturn(true);

        $this->useCase->execute($this->data(['fin' => ['']]));

        $this->assertSame('2025-03-15', $guardado->getF_fin()->format('Y-m-d'));
    }

    public function test_si_el_horario_ya_existe_se_reutiliza(): void
    {
        $this->encargosSacd->method('getNewId')->willReturn(900);
        $this->encargosSacd->method('Guardar')->willReturn(true);
        $existente = new EncargoSacdHorario();
        $existente->setId_item(777);
        $this->horarios->method('findById')->willReturn($existente);
        $this->horarios->expects($this->never())->method('getNewId');
        $this->horarios->expects($this->once())->method('Guardar')->with($existente)->willReturn(true);

        $this->useCase->execute($this->data());

        $this->assertSame(777, $existente->getId_item());
        $this->assertSame(900, $existente->getId_item_tarea_sacd());
    }

    public function test_errores_de_guardado_en_el_alta_se_acumulan_en_los_mensajes(): void
    {
        $this->encargosSacd->method('getNewId')->willReturn(900);
        $this->horarios->method('getNewId')->willReturn(800);
        $this->encargosSacd->method('Guardar')->willReturn(false);
        $this->encargosSacd->method('getErrorTxt')->willReturn('fallo sacd');
        $this->horarios->method('Guardar')->willReturn(false);
        $this->horarios->method('getErrorTxt')->willReturn('fallo horario');

        $out = $this->useCase->execute($this->data());

        $this->assertTrue($out['error']);
        $this->assertStringContainsString('fallo sacd', $out['mensajes']);
        $this->assertStringContainsString('fallo horario', $out['mensajes']);
    }

    // ---- modificación (id_item distinto de 0) -----------------------------------------------------

    public function test_una_fila_existente_actualiza_las_fechas_del_encargo_y_de_sus_horarios(): void
    {
        $sacd = $this->sacd(900);
        $this->encargosSacd->method('findById')->with(900)->willReturn($sacd);
        $this->encargosSacd->expects($this->once())->method('Guardar')->with($sacd)->willReturn(true);
        $h = new EncargoSacdHorario();
        $this->horarios->method('matching')->willReturn([$h]);
        $this->horarios->expects($this->once())->method('Guardar')->with($h)->willReturn(true);

        $out = $this->useCase->execute($this->data(['id_item' => ['900']]));

        $this->assertSame(['error' => false, 'mensajes' => ''], $out);
        $this->assertSame('2025-03-15', $sacd->getF_ini()->format('Y-m-d'));
        $this->assertSame('2025-03-20', $sacd->getF_fin()->format('Y-m-d'));
        $this->assertSame('2025-03-20', $h->getF_fin()->format('Y-m-d'));
    }

    public function test_modificar_un_encargo_que_no_existe_avisa(): void
    {
        $this->encargosSacd->method('findById')->willReturn(null);
        $this->encargosSacd->expects($this->never())->method('Guardar');

        $out = $this->useCase->execute($this->data(['id_item' => ['900']]));

        $this->assertTrue($out['error']);
        $this->assertNotSame('', $out['mensajes']);
    }

    public function test_sin_fechas_se_elimina_el_encargo(): void
    {
        $sacd = $this->sacd(900);
        $this->encargosSacd->method('findById')->willReturn($sacd);
        $this->encargosSacd->expects($this->once())->method('Eliminar')->with($sacd)->willReturn(true);
        $this->encargosSacd->expects($this->never())->method('Guardar');

        $out = $this->useCase->execute($this->data(['id_item' => ['900'], 'inicio' => [''], 'fin' => ['']]));

        $this->assertSame(['error' => false, 'mensajes' => ''], $out);
    }

    public function test_si_falla_eliminar_se_informa(): void
    {
        $this->encargosSacd->method('findById')->willReturn($this->sacd(900));
        $this->encargosSacd->method('Eliminar')->willReturn(false);
        $this->encargosSacd->method('getErrorTxt')->willReturn('no se puede borrar');

        $out = $this->useCase->execute($this->data(['id_item' => ['900'], 'inicio' => [''], 'fin' => ['']]));

        $this->assertTrue($out['error']);
        $this->assertStringContainsString('no se puede borrar', $out['mensajes']);
    }

    public function test_si_falla_el_guardado_se_informa_pero_se_siguen_actualizando_los_horarios(): void
    {
        $this->encargosSacd->method('findById')->willReturn($this->sacd(900));
        $this->encargosSacd->method('Guardar')->willReturn(false);
        $this->encargosSacd->method('getErrorTxt')->willReturn('fallo sacd');
        $this->horarios->method('matching')->willReturn([new EncargoSacdHorario()]);
        $this->horarios->expects($this->once())->method('Guardar')->willReturn(true);

        $out = $this->useCase->execute($this->data(['id_item' => ['900']]));

        $this->assertTrue($out['error']);
        $this->assertStringContainsString('fallo sacd', $out['mensajes']);
    }

    public function test_los_horarios_se_buscan_por_encargo_sacd_e_item(): void
    {
        $this->encargosSacd->method('findById')->willReturn($this->sacd(900));
        $this->encargosSacd->method('Guardar')->willReturn(true);
        $this->horarios->expects($this->once())->method('matching')->with($this->callback(function (\src\shared\domain\criteria\Criteria $c): bool {
            $this->assertSame(['id_enc eq 5', 'id_nom eq 10011', 'id_item_tarea_sacd eq 900'], \Tests\unit\planning\support\DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->useCase->execute($this->data(['id_item' => ['900']]));
    }

    // ---- varias filas y datos incompletos -----------------------------------------------------------

    public function test_procesa_cada_fila_con_sus_propios_valores(): void
    {
        $this->encargosSacd->method('getNewId')->willReturnOnConsecutiveCalls(900, 901);
        $this->horarios->method('getNewId')->willReturn(800);
        $this->horarios->method('Guardar')->willReturn(true);
        $guardados = [];
        $this->encargosSacd->method('Guardar')->willReturnCallback(function (EncargoSacd $o) use (&$guardados): bool {
            $guardados[] = [$o->getId_item(), $o->getId_enc(), $o->getF_ini()->format('Y-m-d')];

            return true;
        });

        $this->useCase->execute($this->data([
            'enc_num' => '2',
            'inicio' => [$this->local('2025-03-15'), $this->local('2025-04-01')],
            'fin' => ['', ''],
            'id_enc' => ['5', '6'],
            'id_item' => ['0', '0'],
        ]));

        $this->assertSame([[900, 5, '2025-03-15'], [901, 6, '2025-04-01']], $guardados);
    }

    public function test_sin_filas_no_hace_nada_y_no_hay_error(): void
    {
        $this->encargosSacd->expects($this->never())->method('Guardar');

        $this->assertSame(['error' => false, 'mensajes' => ''], $this->useCase->execute(['enc_num' => '0', 'id_nom' => '10011']));
    }

    public function test_los_arrays_ausentes_se_tratan_como_vacios(): void
    {
        $this->encargosSacd->method('getNewId')->willReturn(900);
        $this->horarios->method('getNewId')->willReturn(800);
        $this->encargosSacd->method('Guardar')->willReturn(true);
        $this->horarios->method('Guardar')->willReturn(true);

        // Sin fechas, id_enc=0 e id_item=0: se crea un encargo vacío (comportamiento actual, sin validar).
        $out = $this->useCase->execute(['enc_num' => '1', 'id_nom' => '10011']);

        $this->assertSame(['error' => false, 'mensajes' => ''], $out);
    }
}
