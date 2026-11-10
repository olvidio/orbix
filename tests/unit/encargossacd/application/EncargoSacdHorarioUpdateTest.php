<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoSacdHorarioUpdate;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacdHorario;

final class EncargoSacdHorarioUpdateTest extends TestCase
{
    private EncargoSacdHorarioRepositoryInterface&MockObject $repo;
    private EncargoSacdHorarioUpdate $useCase;

    protected function setUp(): void
    {
        $this->repo = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $this->useCase = new EncargoSacdHorarioUpdate($this->repo);
    }

    /** @return array<string, mixed> */
    private function post(array $extra = []): array
    {
        return array_merge([
            'mod' => '1', 'id_nom' => '10011', 'id_enc' => '7', 'dia' => 'l', 'f_ini' => '2025-03-15', 'f_fin' => '',
            'dia_ref' => 'v', 'dia_num' => '2', 'mas_menos' => '+', 'dia_inc' => '3',
            'h_ini' => '09:30:00', 'h_fin' => '10:45:00',
        ], $extra);
    }

    private function capturar(?EncargoSacdHorario &$guardada): void
    {
        $this->repo->method('Guardar')->willReturnCallback(function (EncargoSacdHorario $o) use (&$guardada): bool {
            $guardada = $o;

            return true;
        });
    }

    // ---- eliminar ------------------------------------------------------------------------------

    public function test_eliminar_borra_primero_las_excepciones_y_luego_el_horario(): void
    {
        $o = new EncargoSacdHorario();
        $this->repo->method('findById')->with(8)->willReturn($o);
        $orden = [];
        $this->repo->expects($this->once())->method('eliminarExcepcionesPorHorarioId')->with(8)
            ->willReturnCallback(function () use (&$orden): bool {
                $orden[] = 'excepciones';

                return true;
            });
        $this->repo->expects($this->once())->method('Eliminar')->with($o)
            ->willReturnCallback(function () use (&$orden): bool {
                $orden[] = 'horario';

                return true;
            });

        $this->assertSame(['ok' => true], $this->useCase->ejecutar(['mod' => 'eliminar', 'id_item' => '8']));
        $this->assertSame(['excepciones', 'horario'], $orden);
    }

    /**
     * @dataProvider idsInvalidosProvider
     */
    public function test_eliminar_con_id_invalido_es_error(string $id): void
    {
        $this->repo->expects($this->never())->method('findById');

        $this->assertArrayHasKey('_error', $this->useCase->ejecutar(['mod' => 'eliminar', 'id_item' => $id]));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function idsInvalidosProvider(): iterable
    {
        yield 'cero' => ['0'];
        yield 'negativo' => ['-4'];
        yield 'ausente' => [''];
        yield 'texto' => ['abc'];
    }

    public function test_eliminar_un_horario_inexistente_es_error(): void
    {
        $this->repo->method('findById')->willReturn(null);
        $this->repo->expects($this->never())->method('Eliminar');

        $this->assertArrayHasKey('_error', $this->useCase->ejecutar(['mod' => 'eliminar', 'id_item' => '8']));
    }

    public function test_si_fallan_las_excepciones_no_se_borra_el_horario(): void
    {
        $this->repo->method('findById')->willReturn(new EncargoSacdHorario());
        $this->repo->method('eliminarExcepcionesPorHorarioId')->willReturn(false);
        $this->repo->method('getErrorTxt')->willReturn('error en excepciones');
        $this->repo->expects($this->never())->method('Eliminar');

        $this->assertSame(['_error' => 'error en excepciones'], $this->useCase->ejecutar(['mod' => 'eliminar', 'id_item' => '8']));
    }

    public function test_si_falla_el_borrado_del_horario_devuelve_el_error(): void
    {
        $this->repo->method('findById')->willReturn(new EncargoSacdHorario());
        $this->repo->method('eliminarExcepcionesPorHorarioId')->willReturn(true);
        $this->repo->method('Eliminar')->willReturn(false);
        $this->repo->method('getErrorTxt')->willReturn('error en horario');

        $this->assertSame(['_error' => 'error en horario'], $this->useCase->ejecutar(['mod' => 'eliminar', 'id_item' => '8']));
    }

    // ---- alta (modos 1 y 3) ---------------------------------------------------------------------------

    /**
     * @dataProvider modosDeAltaProvider
     */
    public function test_los_modos_1_y_3_crean_un_horario_nuevo(string $mod): void
    {
        $this->repo->method('getNewId')->willReturn(901);
        $this->capturar($g);

        $this->assertSame(['ok' => true], $this->useCase->ejecutar($this->post(['mod' => $mod])));

        $this->assertSame(901, $g->getId_item());
        $this->assertSame(7, $g->getId_enc());
        $this->assertSame(10011, $g->getId_nom());
        $this->assertSame('2025-03-15', $g->getF_ini()->format('Y-m-d'));
        $this->assertNull($g->getF_fin());
        $this->assertSame('v', $g->getDia_ref());
        $this->assertSame(2, $g->getDia_num());
        $this->assertSame('+', $g->getMas_menos());
        $this->assertSame(3, $g->getDia_inc());
        $this->assertSame('09:30:00', $g->getH_ini()->format('H:i:s'));
        $this->assertSame('10:45:00', $g->getH_fin()->format('H:i:s'));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function modosDeAltaProvider(): iterable
    {
        yield 'modo 1' => ['1'];
        yield 'modo 3' => ['3'];
    }

    /**
     * @dataProvider camposObligatoriosProvider
     * @param array<string, string> $cambio
     */
    public function test_alta_y_edicion_exigen_fecha_inicio_y_dia(string $mod, array $cambio): void
    {
        $this->repo->expects($this->never())->method('Guardar');

        $out = $this->useCase->ejecutar($this->post($cambio + ['mod' => $mod, 'id_item' => '5']));

        $this->assertStringContainsString('(*)', $out['_error']);
    }

    /**
     * @return iterable<string, array{string, array<string, string>}>
     */
    public static function camposObligatoriosProvider(): iterable
    {
        foreach (['1', '2', '3'] as $mod) {
            yield "modo $mod sin f_ini" => [$mod, ['f_ini' => '']];
            yield "modo $mod sin dia" => [$mod, ['dia' => '']];
        }
    }

    /**
     * @dataProvider sinMasMenosProvider
     */
    public function test_sin_mas_menos_el_dia_es_la_referencia_y_mas_menos_queda_null(string $masMenos): void
    {
        $this->capturar($g);

        $this->useCase->ejecutar($this->post(['mas_menos' => $masMenos, 'dia_ref' => 'x', 'dia' => 'm']));

        $this->assertSame('m', $g->getDia_ref());
        $this->assertNull($g->getMas_menos(), 'a diferencia de EncargoHorarioUpdate, aquí «sin más/menos» se guarda como null');
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function sinMasMenosProvider(): iterable
    {
        yield 'vacío' => [''];
        yield 'cero' => ['0'];
    }

    public function test_cero_y_vacios_se_guardan_como_null(): void
    {
        $this->capturar($g);

        $this->useCase->ejecutar($this->post(['dia_num' => '0', 'h_ini' => '', 'h_fin' => '', 'dia_ref' => '', 'mas_menos' => '-', 'dia' => 'l']));

        $this->assertNull($g->getDia_num());
        $this->assertNull($g->getH_ini());
        $this->assertNull($g->getH_fin());
        $this->assertNull($g->getDia_ref());
        $this->assertSame('-', $g->getMas_menos());
    }

    public function test_si_falla_el_guardado_de_un_alta_devuelve_el_error(): void
    {
        $this->repo->method('Guardar')->willReturn(false);
        $this->repo->method('getErrorTxt')->willReturn('violación de clave');

        $out = $this->useCase->ejecutar($this->post());

        $this->assertStringContainsString('violación de clave', $out['_error']);
    }

    // ---- edición (modo 2) ----------------------------------------------------------------------------------

    public function test_el_modo_2_actualiza_el_horario_existente_sin_cambiar_id_encargo_ni_sacd(): void
    {
        $o = new EncargoSacdHorario();
        $o->setId_item(55);
        $o->setId_enc(99);
        $o->setId_nom(10050);
        $this->repo->expects($this->once())->method('findById')->with(55)->willReturn($o);
        $this->repo->expects($this->never())->method('getNewId');
        $this->capturar($g);

        $out = $this->useCase->ejecutar($this->post(['mod' => '2', 'id_item' => '55']));

        $this->assertSame(['ok' => true], $out);
        $this->assertSame($o, $g);
        $this->assertSame(99, $g->getId_enc());
        $this->assertSame(10050, $g->getId_nom());
        $this->assertSame(3, $g->getDia_inc());
        $this->assertSame('2025-03-15', $g->getF_ini()->format('Y-m-d'));
    }

    public function test_el_modo_2_sin_id_valido_es_error(): void
    {
        $this->repo->expects($this->never())->method('findById');

        $this->assertArrayHasKey('_error', $this->useCase->ejecutar($this->post(['mod' => '2', 'id_item' => '0'])));
    }

    public function test_el_modo_2_con_horario_inexistente_es_error(): void
    {
        $this->repo->method('findById')->willReturn(null);
        $this->repo->expects($this->never())->method('Guardar');

        $this->assertArrayHasKey('_error', $this->useCase->ejecutar($this->post(['mod' => '2', 'id_item' => '55'])));
    }

    public function test_el_modo_2_si_falla_el_guardado_devuelve_el_error(): void
    {
        $this->repo->method('findById')->willReturn(new EncargoSacdHorario());
        $this->repo->method('Guardar')->willReturn(false);
        $this->repo->method('getErrorTxt')->willReturn('boom');

        $out = $this->useCase->ejecutar($this->post(['mod' => '2', 'id_item' => '55']));

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
        yield '4' => ['4'];
        yield '0' => ['0'];
        yield 'texto del otro caso de uso' => ['nuevo'];
    }
}
