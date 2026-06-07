<?php

namespace Tests\unit\encargossacd\domain\services;

use PHPUnit\Framework\TestCase;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\services\EncargoDominioService;
use src\encargossacd\domain\value_objects\DiaRefCode;

/**
 * Unitarios puros para {@see EncargoDominioService}.
 *
 * Los tres primeros metodos (`calcular_dia`, `texto_horario`,
 * `texto_horario_ex`) son logica pura: no tocan sesion ni repos.
 * `dedicacion_horas` consulta un repo via `$GLOBALS['container']`; se
 * mockea con un contenedor anonimo minimal.
 *
 * `db_txt_h_sacd` toca `$GLOBALS['oDBE']` directamente con SQL crudo y
 * no compone bien sin BD real; se deja fuera del alcance unitario.
 */
final class EncargoDominioServiceTest extends TestCase
{
    private EncargoDominioService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EncargoDominioService(
            $this->createMock(EncargoSacdHorarioRepositoryInterface::class)
        );
    }

    // ============================================================
    // calcular_dia
    // ============================================================

    public function test_calcular_dia_sin_mas_menos_devuelve_dia_ref(): void
    {
        $this->assertSame(3, $this->service->calcular_dia('', 3, 5));
    }

    public function test_calcular_dia_mas_suma_sin_pasarse_de_la_semana(): void
    {
        // 3 (miercoles) + 2 = 5 (viernes).
        $this->assertSame(5, $this->service->calcular_dia('+', 3, 2));
    }

    public function test_calcular_dia_mas_envuelve_cuando_pasa_de_7(): void
    {
        // 6 (sabado) + 3 = 9 -> 9 - 7 = 2 (martes).
        $this->assertSame(2, $this->service->calcular_dia('+', 6, 3));
    }

    public function test_calcular_dia_menos_resta_sin_bajar_de_cero(): void
    {
        // 5 (viernes) - 2 = 3 (miercoles).
        $this->assertSame(3, $this->service->calcular_dia('-', 5, 2));
    }

    public function test_calcular_dia_menos_envuelve_cuando_queda_negativo(): void
    {
        // 2 (martes) - 5 = -3 -> 7 + (-3) = 4 (jueves).
        $this->assertSame(4, $this->service->calcular_dia('-', 2, 5));
    }

    public function test_calcular_dia_con_mas_menos_pero_dia_inc_vacio_devuelve_cadena_vacia(): void
    {
        // Rama del if: `empty($dia_inc)` corta, $dia se queda en ''.
        $this->assertSame('', $this->service->calcular_dia('+', 3, 0));
        $this->assertSame('', $this->service->calcular_dia('-', 3, null));
    }

    // ============================================================
    // texto_horario
    // ============================================================

    public function test_texto_horario_dia_directo_sin_ordinal(): void
    {
        // mas_menos vacio, dia_num vacio -> "lunes, de 9:00 a 13:00".
        $txt = $this->service->texto_horario('', 1, 0, '', '9:00', '13:00');
        $this->assertSame('lunes, de 9:00 a 13:00', $txt);
    }

    public function test_texto_horario_con_ordinal(): void
    {
        // dia_num=2 -> "el segundo <dia>".
        $txt = $this->service->texto_horario('', 3, 0, 2, '10:00', '11:00');
        $this->assertSame('el segundo miércoles, de 10:00 a 11:00', $txt);
    }

    public function test_texto_horario_antes_del_dia_ref(): void
    {
        // mas_menos='-': calcular_dia(-,2,1) = 1 (lunes); dia_ref=2 (martes), dia_num=1.
        $txt = $this->service->texto_horario('-', 2, 1, 1, '8:00', '9:00');
        $this->assertSame('lunes antes del primer martes, de 8:00 a 9:00', $txt);
    }

    public function test_texto_horario_despues_del_dia_ref(): void
    {
        // mas_menos='+': calcular_dia(+,5,1)=6 (sabado); dia_ref=5 (viernes), dia_num=1.
        $txt = $this->service->texto_horario('+', 5, 1, 1, '12:00', '14:00');
        $this->assertSame('sábado después del primer viernes, de 12:00 a 14:00', $txt);
    }

    public function test_texto_horario_anyade_sufijo_n_sacd(): void
    {
        $txt = $this->service->texto_horario('', 4, 0, '', '15:00', '17:00', 3);
        $this->assertSame('jueves, de 15:00 a 17:00 (3 sacd)', $txt);
    }

    public function test_texto_horario_con_mas_menos_desconocido_devuelve_cadena_vacia(): void
    {
        // Si `$mas_menos` no es '' ni '-' ni '+', ninguna rama asigna
        // `$dia_txt` y el `if (!empty(...))` final deja el texto en ''.
        $txt = $this->service->texto_horario('x', 3, 1, 1, '9:00', '10:00');
        $this->assertSame('', $txt);
    }

    // ============================================================
    // texto_horario_ex
    // ============================================================

    public function test_texto_horario_ex_con_mes_cambia_a_nuevo_horario(): void
    {
        // mes=3 (marzo), horario='t' -> cambia a texto_horario(...).
        $txt = $this->service->texto_horario_ex(
            mes: 3,
            f_ini: '',
            f_fin: '',
            horario: 't',
            mas_menos: '',
            dia_ref: 1,
            dia_inc: 0,
            dia_num: '',
            h_ini: '9:00',
            h_fin: '10:00',
            n_sacd: '',
        );
        $this->assertSame('excepto el mes de marzo que se cambia a: lunes, de 9:00 a 10:00', $txt);
    }

    public function test_texto_horario_ex_sin_mes_usa_rango_de_fechas(): void
    {
        // mes=0 -> "excpeto del X al Y" (typo original en el codigo).
        // horario != 't' -> "se anula".
        $txt = $this->service->texto_horario_ex(
            mes: 0,
            f_ini: '01/03/2030',
            f_fin: '15/03/2030',
            horario: '',
            mas_menos: '',
            dia_ref: 1,
            dia_inc: 0,
            dia_num: '',
            h_ini: '',
            h_fin: '',
            n_sacd: '',
        );
        $this->assertSame('excpeto del 01/03/2030 al 15/03/2030 que se anula', $txt);
    }

    // ============================================================
    // dedicacion_horas
    // ============================================================

    public function test_dedicacion_horas_sin_registros_devuelve_false(): void
    {
        $repo = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $repo->method('getEncargoSacdHorarios')->willReturn([]);

        $service = new EncargoDominioService($repo);
        $this->assertFalse($service->dedicacion_horas(123, 45));
    }

    public function test_dedicacion_horas_suma_segun_tipo_de_horario(): void
    {
        // m -> x5, t -> x2, v -> x3. Esperado: 2*5 + 3*2 + 4*3 = 10+6+12 = 28.
        $repo = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $repo->method('getEncargoSacdHorarios')->willReturn([
            $this->horarioStub('m', 2),
            $this->horarioStub('t', 3),
            $this->horarioStub('v', 4),
        ]);

        $service = new EncargoDominioService($repo);
        $this->assertSame(28, $service->dedicacion_horas(1, 1));
    }

    public function test_dedicacion_horas_ignora_tipos_desconocidos(): void
    {
        $repo = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $repo->method('getEncargoSacdHorarios')->willReturn([
            $this->horarioStub('x', 10),
        ]);

        $service = new EncargoDominioService($repo);
        $this->assertSame(0, $service->dedicacion_horas(1, 1));
    }

    public function test_dedicacion_horas_con_dia_ref_nulo_no_suma(): void
    {
        $horario = new class {
            public function getDia_inc(): int { return 5; }
            public function getDiaRefVo(): ?DiaRefCode { return null; }
        };
        $repo = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $repo->method('getEncargoSacdHorarios')->willReturn([$horario]);

        $service = new EncargoDominioService($repo);
        $this->assertSame(0, $service->dedicacion_horas(1, 1));
    }

    // ============================================================
    // Helpers
    // ============================================================

    private function horarioStub(string $dia_ref, int $dia_inc): object
    {
        return new class($dia_ref, $dia_inc) {
            public function __construct(private readonly string $diaRef, private readonly int $diaInc) {}
            public function getDia_inc(): int { return $this->diaInc; }
            public function getDiaRefVo(): DiaRefCode { return new DiaRefCode($this->diaRef); }
        };
    }
}
