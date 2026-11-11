<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\FormAsignaturasDeUnaActividadData;
use src\actividadestudios\application\ProfesoresDesplegableData;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\profesores\domain\ProfesorActividad;
use src\profesores\domain\services\ProfesorAsignaturaService;
use src\profesores\domain\services\ProfesorStgrService;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;

/** Formulario de una asignatura de un ca: edición (profesor, avisos, fechas) y alta (desplegables). */
final class FormAsignaturasDeUnaActividadDataTest extends TestCase
{
    private const ID_ACTIV = 3001;
    private const ID_ASIG = 1101;

    private ActividadAsignaturaDlRepositoryInterface&MockObject $actividadAsignaturas;
    private ProfesorAsignaturaService&MockObject $profesoresAsignatura;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private PersonaDlRepositoryInterface&MockObject $personasDl;
    private ProfesorActividad&MockObject $profesorActividad;
    /** @var array<string, mixed> */
    private array $sesionAnterior = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION ?? [];
        // Formato de fecha local d/m/a.
        $_SESSION['session_auth']['idioma'] = 'es_ES.UTF-8';

        $this->actividadAsignaturas = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $this->profesoresAsignatura = $this->createMock(ProfesorAsignaturaService::class);
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->personasDl = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->profesorActividad = $this->createMock(ProfesorActividad::class);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->sesionAnterior;
        parent::tearDown();
    }

    private function uc(): FormAsignaturasDeUnaActividadData
    {
        return new FormAsignaturasDeUnaActividadData(
            $this->actividadAsignaturas,
            $this->profesoresAsignatura,
            $this->asignaturas,
            new ProfesoresDesplegableData(
                $this->profesoresAsignatura,
                $this->createMock(ProfesorStgrService::class),
                $this->personasDl,
                $this->profesorActividad,
            ),
            $this->profesorActividad,
        );
    }

    private function asignaturaDelCa(?int $idProfesor = null, ?string $aviso = null, ?string $tipo = null, bool $fechas = false): void
    {
        $a = new ActividadAsignatura();
        $a->setId_activ(self::ID_ACTIV);
        $a->setId_asignatura(self::ID_ASIG);
        $a->setId_profesor($idProfesor);
        $a->setAvis_profesor($aviso);
        $a->setTipo($tipo);
        if ($fechas) {
            $a->setF_ini(new DateTimeLocal('2026-07-01'));
            $a->setF_fin(new DateTimeLocal('2026-07-12'));
        }
        $this->actividadAsignaturas->method('findById')->with(self::ID_ACTIV, self::ID_ASIG)->willReturn($a);
    }

    private function asignatura(?string $nombreCorto = 'Lógica'): void
    {
        $a = $this->createMock(Asignatura::class);
        $a->method('getNombre_corto')->willReturn($nombreCorto);
        $this->asignaturas->method('findById')->with(self::ID_ASIG)->willReturn($a);
    }

    /** @param array<int, string> $profesores */
    private function profesoresDeLaAsignatura(array $profesores): void
    {
        $this->profesoresAsignatura->method('getArrayTodosProfesoresAsignatura')
            ->with($this->callback(static fn (AsignaturaId $id): bool => $id->value() === self::ID_ASIG))
            ->willReturn($profesores);
    }

    public function test_edicion_devuelve_los_datos_de_la_asignatura_y_el_token_de_guardado(): void
    {
        $this->asignaturaDelCa(20001, 'a', 'p', true);
        $this->asignatura();
        $this->profesoresDeLaAsignatura([20001 => 'Vidal, Pere', 20002 => 'Soler, Marc']);
        $this->profesorActividad->expects($this->never())->method('getArrayProfesoresActividad');

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV, 'id_asignatura' => self::ID_ASIG]);

        $this->assertSame('editar', $r['mod']);
        $this->assertSame(self::ID_ACTIV, $r['id_activ']);
        $this->assertSame(self::ID_ASIG, $r['id_asignatura']);
        $this->assertSame('Lógica', $r['nombre_corto']);
        $this->assertSame(['selected', '', 'selected'], [$r['chk_avisado'], $r['chk_confirmado'], $r['chk_preceptor']]);
        $this->assertSame(['1/7/2026', '12/7/2026'], [$r['f_ini'], $r['f_fin']]);
        $this->assertSame([20001 => 'Vidal, Pere', 20002 => 'Soler, Marc'], $r['oDesplProfesores_opciones']);
        $this->assertSame(20001, $r['id_profesor_sel']);
        $this->assertSame([], $r['oDesplAsignaturas_opciones']);
        $this->assertSame('id_activ=3001 AND id_asignatura=1101', $r['primary_key_s']);
        $this->assertSame('f_ini!f_fin!tipo!id_profesor', $r['camposForm']);
        $this->assertSame('', $r['aviso']);
        $ocultos = $r['a_camposHidden'];
        $this->assertSame(['id_activ', 'id_asignatura', 'primary_key_s', 'ctx_editar'], array_keys($ocultos));
        $this->assertSame(
            ['id_activ' => self::ID_ACTIV, 'id_asignatura' => self::ID_ASIG],
            HashB::open($ocultos['ctx_editar'], 'actividad_asignatura_editar')
        );
    }

    public function test_el_aviso_confirmado_y_la_asignatura_sin_profesor_ni_fechas(): void
    {
        $this->asignaturaDelCa(null, 'c');
        $this->asignatura(null);
        $this->profesoresDeLaAsignatura([20002 => 'Soler, Marc']);

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV, 'id_asignatura' => self::ID_ASIG]);

        $this->assertSame(['', 'selected', ''], [$r['chk_avisado'], $r['chk_confirmado'], $r['chk_preceptor']]);
        $this->assertSame(['', ''], [$r['f_ini'], $r['f_fin']]);
        $this->assertSame(-1, $r['id_profesor_sel']);
        $this->assertSame('', $r['nombre_corto']);
        $this->assertSame([20002 => 'Soler, Marc'], $r['oDesplProfesores_opciones']);
    }

    public function test_un_profesor_asignado_que_ya_no_esta_entre_los_de_la_asignatura_se_anyade_al_principio(): void
    {
        $this->asignaturaDelCa(20009);
        $this->asignatura();
        $this->profesoresDeLaAsignatura([20002 => 'Soler, Marc']);
        $p = $this->createMock(PersonaDl::class);
        $p->method('getPrefApellidosNombre')->willReturn('Antic, Profe');
        $this->personasDl->method('findById')->with(20009)->willReturn($p);

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV, 'id_asignatura' => self::ID_ASIG]);

        $this->assertSame([20009 => 'Antic, Profe', 0 => '----------', 20002 => 'Soler, Marc'], $r['oDesplProfesores_opciones']);
        $this->assertSame(20009, $r['id_profesor_sel']);
    }

    public function test_la_seleccion_de_la_lista_manda_sobre_los_campos_sueltos(): void
    {
        $this->asignaturaDelCa();
        $this->asignatura();

        $r = $this->uc()->execute([
            'sel' => [self::ID_ACTIV . '#' . self::ID_ASIG, '9#9'],
            'id_activ' => 5,
            'id_asignatura' => 6,
        ]);

        $this->assertSame([self::ID_ACTIV, self::ID_ASIG], [$r['id_activ'], $r['id_asignatura']]);
    }

    public function test_desde_el_dossier_de_la_actividad_el_id_es_el_del_dossier(): void
    {
        $this->asignaturaDelCa();
        $this->asignatura();

        $r = $this->uc()->execute(['pau' => 'a', 'id_pau' => self::ID_ACTIV, 'id_activ' => 5, 'id_asignatura' => self::ID_ASIG]);

        $this->assertSame(self::ID_ACTIV, $r['id_activ']);
    }

    public function test_editar_una_asignatura_que_no_esta_en_el_ca_lanza_excepcion(): void
    {
        $this->actividadAsignaturas->method('findById')->willReturn(null);

        $this->expectException(\RuntimeException::class);

        $this->uc()->execute(['id_activ' => self::ID_ACTIV, 'id_asignatura' => self::ID_ASIG]);
    }

    public function test_editar_una_asignatura_que_ya_no_existe_en_el_catalogo_lanza_excepcion(): void
    {
        $this->asignaturaDelCa();
        $this->asignaturas->method('findById')->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/1101/');

        $this->uc()->execute(['id_activ' => self::ID_ACTIV, 'id_asignatura' => self::ID_ASIG]);
    }

    public function test_alta_ofrece_los_profesores_de_la_actividad_y_las_asignaturas_del_plan_2026(): void
    {
        $this->profesorActividad->expects($this->once())->method('getArrayProfesoresActividad')
            ->with([self::ID_ACTIV])->willReturn([20001 => 'Vidal, Pere']);
        $this->profesorActividad->method('getLastAvisos')->willReturn(['falta el profesor 20007', 'falta el profesor 20008']);
        $this->asignaturas->expects($this->once())->method('getArrayAsignaturasConSeparador')
            ->with(false, PlanEstudios::PLAN_2026)->willReturn([1101 => 'Lógica', 1102 => 'Metafísica']);
        $this->actividadAsignaturas->expects($this->never())->method('findById');

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame('nuevo', $r['mod']);
        $this->assertSame(0, $r['id_asignatura']);
        $this->assertSame('', $r['nombre_corto']);
        $this->assertSame(-1, $r['id_profesor_sel']);
        $this->assertSame([20001 => 'Vidal, Pere'], $r['oDesplProfesores_opciones']);
        $this->assertSame([1101 => 'Lógica', 1102 => 'Metafísica'], $r['oDesplAsignaturas_opciones']);
        $this->assertSame("falta el profesor 20007\nfalta el profesor 20008", $r['aviso']);
        $this->assertSame('f_ini!f_fin!tipo!id_profesor!id_asignatura', $r['camposForm']);
        $this->assertSame('', $r['primary_key_s']);
        $this->assertSame(['id_activ', 'ctx_nueva'], array_keys($r['a_camposHidden']));
        $this->assertSame(['id_activ' => self::ID_ACTIV], HashB::open($r['a_camposHidden']['ctx_nueva'], 'actividad_asignatura_nueva'));
    }

    /**
     * Hallazgo: sin actividad, el alta consulta antes los profesores de la «actividad 0» y después lanza una
     * excepción cuyo texto habla del nombre de la asignatura, no de la actividad que falta.
     */
    public function test_alta_sin_actividad_lanza_excepcion_despues_de_consultar_los_profesores(): void
    {
        $this->profesorActividad->expects($this->once())->method('getArrayProfesoresActividad')->with([0])->willReturn([]);

        $this->expectException(\InvalidArgumentException::class);

        $this->uc()->execute([]);
    }

    public function test_una_seleccion_sin_asignatura_abre_el_alta_de_esa_actividad(): void
    {
        $this->profesorActividad->method('getArrayProfesoresActividad')->willReturn([]);

        $r = $this->uc()->execute(['sel' => [(string) self::ID_ACTIV]]);

        $this->assertSame('nuevo', $r['mod']);
        $this->assertSame(self::ID_ACTIV, $r['id_activ']);
    }
}
