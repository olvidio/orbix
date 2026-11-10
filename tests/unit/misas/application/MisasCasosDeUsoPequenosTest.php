<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoHorario;
use src\misas\application\GuardarHorarioTarea;
use src\misas\application\ModificarEncargosCentrosData;
use src\misas\application\ModificarEncargosData;
use src\misas\application\ModificarInicialesSacdZonaData;
use src\misas\application\QuitarHorarioTarea;
use src\misas\application\support\EncargoDiaTimeHelper;
use src\misas\application\support\IdNomJefeResolver;
use src\misas\application\support\MisasBuildInput;
use src\misas\application\support\PeriodoDateRange;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use Tests\myTest;

/** Casos de uso pequeños de misas: horarios de tarea, desplegables de zonas y utilidades. */
final class MisasCasosDeUsoPequenosTest extends myTest
{
    private mixed $sesionAnterior;
    private mixed $configAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
    }

    public function tearDown(): void
    {
        foreach (['session_auth' => $this->sesionAnterior, 'oConfig' => $this->configAnterior] as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    // ---- QuitarHorarioTarea ----------------------------------------------------------------------

    public function test_quitar_horario_anula_inicio_y_fin_del_horario_de_la_tarea(): void
    {
        $horario = new EncargoHorario();
        $horario->setH_ini(\src\shared\domain\value_objects\TimeLocal::fromString('08:00'));
        $horario->setH_fin(\src\shared\domain\value_objects\TimeLocal::fromString('09:00'));
        $repo = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $repo->method('findById')->with(4)->willReturn($horario);
        $repo->expects($this->once())->method('Guardar')->with($horario)->willReturn(true);

        $r = (new QuitarHorarioTarea($repo))->execute(['id_item' => '4']);

        $this->assertSame(['error' => ''], $r);
        $this->assertNull($horario->getH_ini());
        $this->assertNull($horario->getH_fin());
    }

    public function test_quitar_horario_sin_id_o_sin_horario_o_con_fallo_de_guardado(): void
    {
        $repo = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $repo->expects($this->once())->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('boom');
        $repo->method('findById')->willReturnMap([[4, new EncargoHorario()], [5, null]]);
        $uc = new QuitarHorarioTarea($repo);

        $this->assertNotSame('', $uc->execute([])['error']);
        $this->assertStringContainsString('5', $uc->execute(['id_item' => 5])['error']);
        $this->assertSame(['error' => 'boom'], $uc->execute(['id_item' => 4]));
    }

    // ---- GuardarHorarioTarea ----------------------------------------------------------------------

    public function test_guardar_horario_cambia_solo_las_horas_recibidas(): void
    {
        $horario = new EncargoHorario();
        $horario->setH_ini(\src\shared\domain\value_objects\TimeLocal::fromString('08:00'));
        $horario->setH_fin(\src\shared\domain\value_objects\TimeLocal::fromString('09:00'));
        $repo = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $repo->method('findById')->with(3)->willReturn($horario);
        $repo->expects($this->once())->method('Guardar')->willReturn(true);

        $r = (new GuardarHorarioTarea($repo))->execute(['id_item_h' => '3', 't_start' => '10:30', 't_end' => '']);

        $this->assertSame(['error' => ''], $r);
        $this->assertSame('10:30:00', $horario->getH_ini()->toDatabaseString());
        $this->assertSame('09:00:00', $horario->getH_fin()->toDatabaseString());
    }

    public function test_guardar_horario_valida_id_existencia_y_guardado(): void
    {
        $repo = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $repo->method('findById')->willReturnMap([[3, new EncargoHorario()], [4, null]]);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('x');
        $uc = new GuardarHorarioTarea($repo);

        $this->assertNotSame('', $uc->execute([])['error']);
        $this->assertStringContainsString('4', $uc->execute(['id_item_h' => 4])['error']);
        $this->assertSame(['error' => 'x'], $uc->execute(['id_item_h' => 3]));
    }

    // ---- desplegables de zonas ----------------------------------------------------------------------

    private function usuario(?int $idRole, ?string $pau): Usuario&MockObject
    {
        $u = $this->createMock(Usuario::class);
        $u->method('getId_role')->willReturn($idRole);
        $u->method('getCsvIdPauAsString')->willReturn($pau);

        return $u;
    }

    private function resolver(?Usuario $usuario, array $roles = [1 => 'p-sacd', 2 => 'admin']): IdNomJefeResolver
    {
        $_SESSION['session_auth'] = ['id_usuario' => 55];
        $usuarios = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarios->method('findById')->with(55)->willReturn($usuario);
        $roles_ = $this->createMock(RoleRepositoryInterface::class);
        $roles_->method('getArrayRoles')->willReturn($roles);

        return new IdNomJefeResolver($usuarios, $roles_);
    }

    public function test_el_resolutor_de_jefe_cubre_todos_los_casos(): void
    {
        $this->assertSame('Usuario no encontrado', $this->resolver(null)->resolve()['error']);
        $this->assertSame(['id_nom_jefe' => null, 'error' => ''], $this->resolver($this->usuario(2, '10011'))->resolve());
        $this->assertSame(['id_nom_jefe' => 10011, 'error' => ''], $this->resolver($this->usuario(1, '10011'))->resolve());
        $this->assertNotSame('', $this->resolver($this->usuario(1, null))->resolve()['error']);
        $this->assertNotSame('', $this->resolver($this->usuario(1, 'abc'))->resolve()['error']);
    }

    public function test_el_jefe_de_calendario_no_se_limita_a_su_zona(): void
    {
        $_SESSION['oConfig'] = new class {
            public function is_jefeCalendario(): bool
            {
                return true;
            }
        };

        $this->assertSame(['id_nom_jefe' => null, 'error' => ''], $this->resolver($this->usuario(1, '10011'))->resolve());
    }

    public function test_modificar_encargos_pasa_el_jefe_a_las_zonas_y_ofrece_los_ordenes(): void
    {
        $zonas = $this->createMock(ZonaRepositoryInterface::class);
        $zonas->expects($this->once())->method('getArrayZonas')->with(10011)->willReturn([3 => 'Zona 3']);

        $r = (new ModificarEncargosData($zonas, $this->resolver($this->usuario(1, '10011'))))->getData();

        $this->assertSame('', $r['error']);
        $this->assertSame([3 => 'Zona 3'], $r['a_opciones_zona']);
        $this->assertSame(['orden', 'prioridad', 'desc_enc'], array_keys($r['a_orden']));
    }

    public function test_modificar_encargos_sin_permiso_no_consulta_zonas(): void
    {
        $zonas = $this->createMock(ZonaRepositoryInterface::class);
        $zonas->expects($this->never())->method('getArrayZonas');

        $r = (new ModificarEncargosData($zonas, $this->resolver($this->usuario(1, null))))->getData();
        $c = (new ModificarEncargosCentrosData($zonas, $this->resolver($this->usuario(1, null))))->getData();

        $this->assertSame(['a_opciones_zona' => [], 'a_orden' => []], ['a_opciones_zona' => $r['a_opciones_zona'], 'a_orden' => $r['a_orden']]);
        $this->assertNotSame('', $r['error']);
        $this->assertNotSame('', $c['error']);
        $this->assertSame([], $c['a_opciones_zona']);
    }

    public function test_modificar_encargos_centros_devuelve_las_zonas_del_usuario(): void
    {
        $zonas = $this->createMock(ZonaRepositoryInterface::class);
        $zonas->method('getArrayZonas')->with(null)->willReturn([1 => 'Z1']);

        $r = (new ModificarEncargosCentrosData($zonas, $this->resolver($this->usuario(2, null))))->getData();

        $this->assertSame(['error' => '', 'a_opciones_zona' => [1 => 'Z1']], $r);
    }

    public function test_iniciales_sacd_zona_lista_todas_las_zonas(): void
    {
        $zonas = $this->createMock(ZonaRepositoryInterface::class);
        $zonas->method('getArrayZonas')->with(null)->willReturn([1 => 'Z1']);

        $this->assertSame(['a_opciones' => [1 => 'Z1']], (new ModificarInicialesSacdZonaData($zonas))->getData());
    }

    // ---- utilidades ----------------------------------------------------------------------

    public function test_misas_build_input_convierte_con_valores_por_defecto(): void
    {
        $in = ['i' => 5, 'n' => '12', 'f' => 1.5, 'x' => 'abc', 'a' => [1], 'nul' => null];

        $this->assertSame(5, MisasBuildInput::int($in, 'i'));
        $this->assertSame(12, MisasBuildInput::int($in, 'n'));
        $this->assertSame(7, MisasBuildInput::int($in, 'x', 7));
        $this->assertSame(0, MisasBuildInput::int($in, 'f'));
        $this->assertSame(9, MisasBuildInput::int($in, 'no_existe', 9));
        $this->assertSame('abc', MisasBuildInput::string($in, 'x'));
        $this->assertSame('5', MisasBuildInput::string($in, 'i'));
        $this->assertSame('1.5', MisasBuildInput::string($in, 'f'));
        $this->assertSame('d', MisasBuildInput::string($in, 'a', 'd'));
        $this->assertSame('d', MisasBuildInput::string($in, 'nul', 'd'));
        $this->assertSame('', MisasBuildInput::string($in, 'no_existe'));
    }

    public function test_helper_de_hora_de_encargo(): void
    {
        $dt = new DateTimeLocal('2026-03-04 07:05:00');

        $this->assertSame('07:05', EncargoDiaTimeHelper::hora($dt));
        $this->assertSame('', EncargoDiaTimeHelper::hora(new DateTimeLocal('2026-03-04 00:00:00')), 'medianoche = sin hora');
        $this->assertSame('', EncargoDiaTimeHelper::hora(null));
        $this->assertSame('04/03/2026', EncargoDiaTimeHelper::format($dt, 'd/m/Y'));
        $this->assertSame('', EncargoDiaTimeHelper::format(null, 'd/m/Y'));
    }

    public function test_rango_de_periodo_otro_usa_los_limites_dados(): void
    {
        $this->assertSame(['2026-03-01', '2026-03-31'], PeriodoDateRange::resolve('otro', '01/03/2026', '31/03/2026'));
        $this->assertSame(['x-y', 'z-w'], PeriodoDateRange::resolve('otro', 'x/y', 'z/w'));
    }

    public function test_rangos_de_semana_y_mes_son_coherentes_con_hoy(): void
    {
        $hoy = new \DateTimeImmutable('today');
        [$min, $max] = PeriodoDateRange::resolve('esta_semana', '', '');
        $this->assertLessThanOrEqual($hoy->format('Y-m-d'), $min);
        // Hallazgo: en lunes `esta_semana` retrocede 6 días (martes anterior) en vez de 0; ver plan.
        $this->assertSame(7, (new \DateTimeImmutable($min))->diff(new \DateTimeImmutable($max))->days);

        [$min, $max] = PeriodoDateRange::resolve('proxima_semana', '', '');
        $this->assertSame('1', (new \DateTimeImmutable($min))->format('N'));
        $this->assertGreaterThan($hoy->format('Y-m-d'), $min);
        $this->assertSame(7, (new \DateTimeImmutable($min))->diff(new \DateTimeImmutable($max))->days);

        [$min, $max] = PeriodoDateRange::resolve('este_mes', '', '');
        $this->assertSame($hoy->format('Y-m-01'), $min);
        $this->assertSame($hoy->modify('first day of next month')->format('Y-m-01'), $max);

        [$min, $max] = PeriodoDateRange::resolve('proximo_mes', '', '');
        $this->assertSame($hoy->modify('first day of next month')->format('Y-m-01'), $min);
        $this->assertSame($hoy->modify('first day of +2 months')->format('Y-m-01'), $max);
    }
}
