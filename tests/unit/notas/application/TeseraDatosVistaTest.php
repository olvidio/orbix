<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\AsignaturaName;
use src\asignaturas\domain\value_objects\NivelId;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\application\ExpedienteNotasPersona;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\Tesera;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\personas\domain\entity\PersonaEx;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * `Tesera::datosParaVistaTesera()` y `cursoActual()`: tabla de la tessera (una fila por asignatura del plan, con
 * título de sección al empezar cada año), notas que no entran y estadísticas del curso.
 */
final class TeseraDatosVistaTest extends TestCase
{
    private mixed $configAnterior;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $this->config(9, 6);
    }

    protected function tearDown(): void
    {
        $_SESSION['oConfig'] = $this->configAnterior;
        parent::tearDown();
    }

    /** Curso de `1/$mesIni` a `30/$mesFin` (día de fin fijo a 28 para que valga en cualquier mes). */
    private function config(int $mesIni, int $mesFin): void
    {
        $_SESSION['oConfig'] = new ConfigSnapshot(
            null, null, null, null, null, null, null, null, null, null, 'es_ES.UTF-8', null, null,
            ['ini_dia' => 1, 'ini_mes' => $mesIni, 'fin_dia' => 28, 'fin_mes' => $mesFin],
            null,
        );
    }

    private static function asignatura(int $id, int $idNivel, string $corto, float $creditos, bool $activa = true): Asignatura
    {
        $a = new Asignatura();
        $a->setId_asignatura($id);
        $a->setIdNivelVo(new NivelId($idNivel));
        $a->setNombreAsignaturaVo(new AsignaturaName($corto . ' (largo)'));
        $a->setNombre_corto($corto);
        $a->setCreditos($creditos);
        $a->setActive($activa);

        return $a;
    }

    private function nota(int $idNivel, int $idAsignatura, ?DateTimeLocal $fecha, bool $aprobada, string $nota): PersonaNota
    {
        $n = $this->createStub(PersonaNota::class);
        $n->method('getIdNivelVo')->willReturn(new NivelId($idNivel));
        $n->method('getId_asignatura')->willReturn($idAsignatura);
        $n->method('getTipo_acta')->willReturn(1);
        $n->method('getF_acta')->willReturn($fecha);
        $n->method('getId_situacion')->willReturn($aprobada ? 3 : 2);
        $n->method('isAprobada')->willReturn($aprobada);
        $n->method('getNota_txt')->willReturn($nota);
        $n->method('getActaVo')->willReturn(null);

        return $n;
    }

    /**
     * @param list<Asignatura> $plan
     * @param array<int, Asignatura> $catalogo id_asignatura => asignatura
     * @param list<PersonaNota> $notas
     */
    private function tesera(array $plan, array $catalogo, array $notas): Tesera
    {
        $notasRepo = $this->createStub(PersonaNotaRepositoryInterface::class);
        $notasRepo->method('matching')->willReturn($notas);
        $sinFinCuadrienio = $this->createStub(PersonaNotaRepositoryInterface::class);
        $sinFinCuadrienio->method('matching')->willReturn([]);

        $asignaturas = $this->createStub(AsignaturaRepositoryInterface::class);
        $asignaturas->method('matching')->willReturn($plan);
        $asignaturas->method('findById')->willReturnCallback(static fn (int $id): ?Asignatura => $catalogo[$id] ?? null);

        return new Tesera(new ExpedienteNotasPersona($notasRepo), $asignaturas, new PlanEstudiosDePersona($sinFinCuadrienio));
    }

    private function persona(): PersonaEx
    {
        $p = $this->createStub(PersonaEx::class);
        $p->method('getPrefApellidosNombre')->willReturn('Pérez, Juan');
        $p->method('getCentro_o_dl')->willReturn('Centro A');

        return $p;
    }

    public function test_tabla_con_secciones_pendientes_opcionales_y_estadisticas(): void
    {
        $curso = (new Tesera(
            new ExpedienteNotasPersona($this->createStub(PersonaNotaRepositoryInterface::class)),
            $this->createStub(AsignaturaRepositoryInterface::class),
            new PlanEstudiosDePersona($this->createStub(PersonaNotaRepositoryInterface::class)),
        ))->cursoActual();
        $enElCurso = $curso['inicio'];
        $deOtroCurso = new DateTimeLocal('2001-03-15');

        $fil1 = self::asignatura(1101, 1101, 'Fil. I', 3.0);
        $fil2 = self::asignatura(1102, 1102, 'Fil. II', 2.5);
        $teo1 = self::asignatura(2101, 2101, 'Teo. I', 4.0);
        $opV = self::asignatura(2434, 2434, 'Op. V', 2.0);
        $concreta = self::asignatura(3501, 2434, 'Seminarium', 2.0);
        $inactiva = self::asignatura(1150, 1150, 'Vieja', 1.0, false);
        $plan = [$fil1, $fil2, $teo1, $opV];
        $catalogo = [1101 => $fil1, 1102 => $fil2, 2101 => $teo1, 2434 => $opV, 3501 => $concreta, 1150 => $inactiva];

        $notas = [
            $this->nota(1101, 1101, $enElCurso, true, '9'),
            $this->nota(1102, 1102, $enElCurso, false, 'cursada'),
            $this->nota(2101, 2101, $deOtroCurso, true, '7'),
            $this->nota(2434, 3501, $enElCurso, true, 'probatus'),
            $this->nota(1000, 1001, $enElCurso, true, 'fuera de rango'),
            $this->nota(2499, 9998, $enElCurso, true, 'fin de cuadrienio'),
            $this->nota(1103, 7777, $enElCurso, true, 'asignatura inexistente'),
            $this->nota(1150, 1150, $enElCurso, true, 'asignatura inactiva'),
        ];

        $datos = $this->tesera($plan, $catalogo, $notas)->datosParaVistaTesera(10011, $this->persona());

        $this->assertSame('Pérez, Juan', $datos['ap_nom']);
        $this->assertSame('Centro A', $datos['centro']);
        $tabla = array_values($datos['tabla']);
        $this->assertSame([1101, 1102, 2101, 2434], array_column($tabla, 'id_nivel'), 'solo las del plan, en orden');

        $this->assertSame('filosofia_anno_i', $tabla[0]['titulo_seccion']);
        $this->assertArrayNotHasKey('titulo_seccion', $tabla[1], 'el título solo en la primera fila del año');
        $this->assertSame('teologia_anno_i', $tabla[2]['titulo_seccion']);
        $this->assertSame('teologia_anno_iv', $tabla[3]['titulo_seccion']);

        $this->assertSame(['Fil. I', '9', 't'], [$tabla[0]['asignatura'], $tabla[0]['nota'], $tabla[0]['bAprobada']]);
        $this->assertSame($enElCurso->getFromLocal(), $tabla[0]['fecha']);
        $this->assertSame(['cursada', 'f'], [$tabla[1]['nota'], $tabla[1]['bAprobada']], 'cursada: se ve, no cuenta');
        $this->assertSame('Op. V<br>&nbsp;&nbsp;&nbsp;&nbsp;Seminarium', $tabla[3]['asignatura'], 'opcional: hueco + concreta');

        $this->assertSame(3, $datos['numasig']);
        $this->assertSame(9.0, $datos['numcred']);
        $this->assertSame(2, $datos['numasig_year'], 'solo las aprobadas dentro del curso actual');
        $this->assertSame(5.0, $datos['numcred_year']);
        $this->assertSame(4, $datos['num_asig_total']);
        $this->assertSame(11.5, $datos['num_creditos_total']);
        $this->assertSame($curso['texto'], $datos['curso_txt']);
    }

    public function test_asignatura_sin_nota_sale_como_pendiente(): void
    {
        $fil1 = self::asignatura(1101, 1101, 'Fil. I', 3.0);
        $teo2 = self::asignatura(2201, 2201, 'Teo. II', 3.0);

        $tabla = array_values($this->tesera([$fil1, $teo2], [], [])->datosParaVistaTesera(10011, $this->persona())['tabla']);

        $this->assertSame(['id_nivel' => 1101, 'asignatura' => 'Fil. I', 'nota' => -1, 'fecha' => '', 'bAprobada' => 'f', 'titulo_seccion' => 'filosofia_anno_i'], $tabla[0]);
        $this->assertSame('teologia_anno_ii', $tabla[1]['titulo_seccion']);
    }

    public function test_titulos_de_todos_los_anyos(): void
    {
        $plan = [];
        foreach ([1201, 2301, 2401] as $nivel) {
            $plan[] = self::asignatura($nivel, $nivel, "A$nivel", 1.0);
        }

        $tabla = array_values($this->tesera($plan, [], [])->datosParaVistaTesera(10011, $this->persona())['tabla']);

        $this->assertSame(['filosofia_anno_ii', 'teologia_anno_iii', 'teologia_anno_iv'], array_column($tabla, 'titulo_seccion'));
    }

    public function test_curso_actual_segun_el_mes_de_fin(): void
    {
        $any = (int) date('Y');

        $mes = (int) date('n');
        if ($mes === 1) {
            $this->markTestSkipped('En enero no hay mes de fin anterior al actual');
        }
        $this->config(9, $mes - 1);
        $curso = $this->tesera([], [], [])->cursoActual();
        $this->assertSame(($any - 1) . "-$any", $curso['texto'], 'ya pasado el mes de fin: el curso que acaba este año');
        $this->assertSame(($any - 1) . '-09-01', $curso['inicio']->format('Y-m-d'));

        $this->config(9, 12);
        $curso = $this->tesera([], [], [])->cursoActual();
        $this->assertSame(($any - 2) . '-' . ($any - 1), $curso['texto'], 'antes del mes de fin: el curso anterior');
        $this->assertSame(($any - 1) . '-12-28', $curso['fin']->format('Y-m-d'));
    }

    public function test_sin_configuracion_en_sesion_falla(): void
    {
        unset($_SESSION['oConfig']);

        $this->expectException(\RuntimeException::class);
        $this->tesera([], [], [])->cursoActual();
    }
}
