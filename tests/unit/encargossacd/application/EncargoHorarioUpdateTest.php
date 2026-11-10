<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoHorarioUpdate;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoHorario;

final class EncargoHorarioUpdateTest extends TestCase
{
    private EncargoHorarioRepositoryInterface&MockObject $repo;
    private EncargoHorarioUpdate $useCase;

    protected function setUp(): void
    {
        $this->repo = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $this->useCase = new EncargoHorarioUpdate($this->repo);
    }

    /** @return array<string, mixed> */
    private function post(array $extra = []): array
    {
        return array_merge([
            'mod' => 'nuevo', 'id_enc' => '7', 'dia' => 'l', 'f_ini' => '2025-03-15', 'f_fin' => '',
            'dia_ref' => 'v', 'dia_num' => '2', 'mas_menos' => '+', 'dia_inc' => '3',
            'h_ini' => '09:30:00', 'h_fin' => '10:45:00', 'n_sacd' => '2', 'mes' => '4',
        ], $extra);
    }

    /** Captura la entidad que se guarda. */
    private function capturar(?EncargoHorario &$guardada): void
    {
        $this->repo->method('Guardar')->willReturnCallback(function (EncargoHorario $o) use (&$guardada): bool {
            $guardada = $o;

            return true;
        });
    }

    // ---- eliminar -------------------------------------------------------------------------

    public function test_eliminar_toma_el_id_del_primer_elemento_seleccionado(): void
    {
        $o = new EncargoHorario();
        $this->repo->expects($this->once())->method('findById')->with(5)->willReturn($o);
        $this->repo->expects($this->once())->method('Eliminar')->with($o)->willReturn(true);

        $this->assertSame(['ok' => true], $this->useCase->ejecutar(['mod' => 'eliminar', 'sel_nom' => ['5#loquesea', '9#otro']]));
    }

    /**
     * @dataProvider seleccionesInvalidasProvider
     */
    public function test_eliminar_sin_seleccion_valida_es_error(mixed $sel): void
    {
        $this->repo->expects($this->never())->method('Eliminar');

        $out = $this->useCase->ejecutar(['mod' => 'eliminar', 'sel_nom' => $sel]);

        $this->assertArrayHasKey('_error', $out);
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function seleccionesInvalidasProvider(): iterable
    {
        yield 'no es array' => ['5#x'];
        yield 'array vacío' => [[]];
        yield 'null' => [null];
    }

    public function test_eliminar_un_registro_que_no_existe_se_da_por_bueno_sin_borrar(): void
    {
        $this->repo->method('findById')->willReturn(null);
        $this->repo->expects($this->never())->method('Eliminar');

        $this->assertSame(['ok' => true], $this->useCase->ejecutar(['mod' => 'eliminar', 'sel_nom' => ['5#x']]));
    }

    public function test_eliminar_devuelve_el_error_del_repositorio(): void
    {
        $this->repo->method('findById')->willReturn(new EncargoHorario());
        $this->repo->method('Eliminar')->willReturn(false);
        $this->repo->method('getErrorTxt')->willReturn('fallo sql');

        $this->assertSame(['_error' => 'fallo sql'], $this->useCase->ejecutar(['mod' => 'eliminar', 'sel_nom' => ['5#x']]));
    }

    // ---- nuevo ---------------------------------------------------------------------------------

    /**
     * @dataProvider camposObligatoriosProvider
     * @param array<string, string> $cambio
     */
    public function test_nuevo_exige_fecha_inicio_y_dia(string $mod, array $cambio): void
    {
        $this->repo->expects($this->never())->method('Guardar');

        $out = $this->useCase->ejecutar($this->post($cambio + ['mod' => $mod, 'id_item_h' => '1']));

        $this->assertStringContainsString('(*)', $out['_error']);
    }

    /**
     * @return iterable<string, array{string, array<string, string>}>
     */
    public static function camposObligatoriosProvider(): iterable
    {
        foreach (['nuevo', 'editar'] as $mod) {
            yield "$mod sin f_ini" => [$mod, ['f_ini' => '']];
            yield "$mod sin dia" => [$mod, ['dia' => '']];
        }
    }

    public function test_nuevo_guarda_todos_los_campos_con_un_id_nuevo(): void
    {
        $this->repo->method('getNewId')->willReturn(901);
        $this->capturar($g);

        $this->assertSame(['ok' => true], $this->useCase->ejecutar($this->post()));

        $this->assertSame(901, $g->getId_item_h());
        $this->assertSame(7, $g->getId_enc());
        $this->assertSame('2025-03-15', $g->getF_ini()->format('Y-m-d'));
        $this->assertNull($g->getF_fin());
        $this->assertSame('v', $g->getDia_ref());
        $this->assertSame(2, $g->getDia_num());
        $this->assertSame('+', $g->getMas_menos());
        $this->assertSame(3, $g->getDia_inc());
        $this->assertSame('09:30:00', $g->getH_ini()->format('H:i:s'));
        $this->assertSame('10:45:00', $g->getH_fin()->format('H:i:s'));
        $this->assertSame(2, $g->getN_sacd());
        $this->assertSame(4, $g->getMes());
    }

    /**
     * Sin «más/menos» el día de referencia es el propio día de la semana.
     *
     * @dataProvider sinMasMenosProvider
     */
    public function test_nuevo_sin_mas_menos_toma_el_dia_como_referencia(string $masMenos): void
    {
        $this->capturar($g);

        $this->useCase->ejecutar($this->post(['mas_menos' => $masMenos, 'dia_ref' => 'x', 'dia' => 'm']));

        $this->assertSame('m', $g->getDia_ref());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function sinMasMenosProvider(): iterable
    {
        yield 'vacío' => [''];
        yield 'cero' => ['0'];
    }

    public function test_nuevo_convierte_cero_y_vacios_en_null(): void
    {
        $this->capturar($g);

        $this->useCase->ejecutar($this->post(['dia_num' => '0', 'h_ini' => '', 'h_fin' => '', 'f_fin' => '', 'dia_ref' => '', 'mas_menos' => '+', 'dia' => 'l']));

        $this->assertNull($g->getDia_num());
        $this->assertNull($g->getH_ini());
        $this->assertNull($g->getH_fin());
        $this->assertNull($g->getF_fin());
        $this->assertNull($g->getDia_ref(), 'con «+» y sin dia_ref: queda sin referencia');
    }

    public function test_nuevo_con_fecha_fin_la_guarda(): void
    {
        $this->capturar($g);

        $this->useCase->ejecutar($this->post(['f_fin' => '2025-12-31']));

        $this->assertSame('2025-12-31', $g->getF_fin()->format('Y-m-d'));
    }

    public function test_nuevo_si_falla_el_guardado_devuelve_el_error_del_repositorio(): void
    {
        $this->repo->method('Guardar')->willReturn(false);
        $this->repo->method('getErrorTxt')->willReturn('clave duplicada');

        $out = $this->useCase->ejecutar($this->post());

        $this->assertStringContainsString('clave duplicada', $out['_error']);
    }

    // ---- editar -----------------------------------------------------------------------------------

    public function test_editar_actualiza_el_registro_existente_sin_cambiar_id_ni_encargo(): void
    {
        $o = new EncargoHorario();
        $o->setId_item_h(55);
        $o->setId_enc(99);
        $this->repo->expects($this->once())->method('findById')->with(55)->willReturn($o);
        $this->repo->expects($this->never())->method('getNewId');
        $this->capturar($g);

        $out = $this->useCase->ejecutar($this->post(['mod' => 'editar', 'id_item_h' => '55', 'id_enc' => '7']));

        $this->assertSame(['ok' => true], $out);
        $this->assertSame($o, $g);
        $this->assertSame(55, $g->getId_item_h());
        $this->assertSame(99, $g->getId_enc(), 'editar no cambia el encargo');
        $this->assertSame(3, $g->getDia_inc());
    }

    public function test_editar_un_registro_inexistente_es_error(): void
    {
        $this->repo->method('findById')->willReturn(null);
        $this->repo->expects($this->never())->method('Guardar');

        $out = $this->useCase->ejecutar($this->post(['mod' => 'editar', 'id_item_h' => '55']));

        $this->assertArrayHasKey('_error', $out);
    }

    public function test_editar_si_falla_el_guardado_devuelve_error(): void
    {
        $this->repo->method('findById')->willReturn(new EncargoHorario());
        $this->repo->method('Guardar')->willReturn(false);
        $this->repo->method('getErrorTxt')->willReturn('boom');

        $out = $this->useCase->ejecutar($this->post(['mod' => 'editar', 'id_item_h' => '55']));

        $this->assertStringContainsString('boom', $out['_error']);
    }

    /**
     * @dataProvider modosInvalidosProvider
     */
    public function test_un_modo_desconocido_es_error(string $mod): void
    {
        $this->repo->expects($this->never())->method('Guardar');
        $this->repo->expects($this->never())->method('Eliminar');

        $this->assertArrayHasKey('_error', $this->useCase->ejecutar($this->post(['mod' => $mod])));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function modosInvalidosProvider(): iterable
    {
        yield 'vacío' => [''];
        yield 'inexistente' => ['borrar'];
        yield 'mayúsculas' => ['NUEVO'];
        yield 'numérico del otro caso de uso' => ['1'];
    }
}
