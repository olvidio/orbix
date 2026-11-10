<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\entity\AsignaturaTipo;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\application\ActaImprimirPresentacionData;
use src\notas\application\DatosActa;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\ActaTribunal;
use src\notas\domain\entity\PersonaNota;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

final class ActaImprimirPresentacionDataTest extends myTest
{
    private ActaRepositoryInterface&MockObject $actas;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private AsignaturaTipoRepositoryInterface&MockObject $tipos;
    private ActaTribunalRepositoryInterface&MockObject $tribunalComun;
    private ActaTribunalDlRepositoryInterface&MockObject $tribunalDl;
    private PersonaNotaRepositoryInterface&MockObject $notas;
    private PersonaFinderService&MockObject $personas;
    private mixed $configAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $this->conAmbito('dl');
        $this->actas = $this->createMock(ActaRepositoryInterface::class);
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->tipos = $this->createMock(AsignaturaTipoRepositoryInterface::class);
        $this->tribunalComun = $this->createMock(ActaTribunalRepositoryInterface::class);
        $this->tribunalComun->method('matching')->willReturn([]);
        $this->tribunalDl = $this->createMock(ActaTribunalDlRepositoryInterface::class);
        $this->tribunalDl->method('matching')->willReturn([]);
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $this->notas->method('matching')->willReturn([]);
        $this->personas = $this->createMock(PersonaFinderService::class);
    }

    public function tearDown(): void
    {
        if ($this->configAnterior === null) {
            unset($_SESSION['oConfig']);
        } else {
            $_SESSION['oConfig'] = $this->configAnterior;
        }
        parent::tearDown();
    }

    private function conAmbito(string $ambito): void
    {
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, 'Region Latina', null, null, null, $ambito, null, null, null, null, null, null, ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8], null);
    }

    private function uc(): ActaImprimirPresentacionData
    {
        return new ActaImprimirPresentacionData($this->actas, $this->asignaturas, $this->tipos, $this->tribunalComun, $this->tribunalDl, new DatosActa($this->notas), $this->personas);
    }

    private function acta(): Acta
    {
        $a = new Acta();
        $a->setActa('dlb 12/24');
        $a->setId_asignatura(1101);
        $a->setId_activ(3001);
        $a->setF_acta(new DateTimeLocal('2024-06-15'));
        $a->setLibro(2);
        $a->setPagina(15);
        $a->setLinea(3);
        $a->setLugar('Aula 1');
        $a->setObserv('nota');

        return $a;
    }

    private function asignatura(?int $year = 1, ?int $idTipo = 5, string $nombre = 'Lógica'): Asignatura
    {
        $a = $this->createMock(Asignatura::class);
        $a->method('getNombre_corto')->willReturn('Lóg');
        $a->method('getNombre_asignatura')->willReturn($nombre);
        $a->method('getYear')->willReturn($year === null ? null : (string) $year);
        $a->method('getId_tipo')->willReturn($idTipo);

        return $a;
    }

    private function tipoLatin(string $valor = 'Philosophia'): AsignaturaTipo
    {
        $t = $this->createMock(AsignaturaTipo::class);
        $t->method('getTipoLatinVo')->willReturn(\src\asignaturas\domain\value_objects\AsignaturaTipoLatin::fromNullableString($valor));

        return $t;
    }

    /** Escenario válido: acta, asignatura y tipo existen. */
    private function escenarioBase(?Asignatura $asignatura = null): void
    {
        $this->actas->method('findById')->willReturn($this->acta());
        $this->asignaturas->method('findById')->willReturn($asignatura ?? $this->asignatura());
        $this->tipos->method('findById')->willReturn($this->tipoLatin());
    }

    private function nota(int $idNom, string $txt): PersonaNota
    {
        $n = $this->createMock(PersonaNota::class);
        $n->method('getId_nom')->willReturn($idNom);
        $n->method('getNota_num')->willReturn('8');
        $n->method('getNota_txt')->willReturn($txt);

        return $n;
    }

    private function persona(string $apellidosNombre): PersonaDl
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getApellidosUpperNombre')->willReturn($apellidosNombre);

        return $p;
    }

    /** @param array<int, ?PersonaDl> $porId */
    private function conAlumnos(array $notas, array $porId): void
    {
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $this->notas->method('matching')->willReturn($notas);
        $this->personas->method('findPersonaEnGlobalODePaso')->willReturnCallback(static fn (int $id) => $porId[$id] ?? null);
    }

    // ---- errores -------------------------------------------------------------------------------------------

    public function test_sin_acta_lanza_excepcion_de_argumento(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->uc()->execute('', 'imprimir');
    }

    public function test_un_acta_inexistente_lanza_excepcion(): void
    {
        $this->actas->method('findById')->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('dlb 99/24');
        $this->uc()->execute('dlb 99/24', 'imprimir');
    }

    public function test_un_acta_sin_asignatura_lanza_excepcion(): void
    {
        $acta = $this->createMock(Acta::class);
        $acta->method('getId_asignatura')->willReturn(null);
        $this->actas->method('findById')->willReturn($acta);

        $this->expectException(\RuntimeException::class);
        $this->uc()->execute('dlb 12/24', 'imprimir');
    }

    public function test_una_asignatura_inexistente_lanza_excepcion(): void
    {
        $this->actas->method('findById')->willReturn($this->acta());
        $this->asignaturas->method('findById')->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('1101');
        $this->uc()->execute('dlb 12/24', 'imprimir');
    }

    public function test_una_asignatura_sin_tipo_lanza_excepcion(): void
    {
        $this->actas->method('findById')->willReturn($this->acta());
        $this->asignaturas->method('findById')->willReturn($this->asignatura(1, null));

        $this->expectException(\RuntimeException::class);
        $this->uc()->execute('dlb 12/24', 'imprimir');
    }

    public function test_un_tipo_de_asignatura_inexistente_lanza_excepcion(): void
    {
        $this->actas->method('findById')->willReturn($this->acta());
        $this->asignaturas->method('findById')->willReturn($this->asignatura(1, 5));
        $this->tipos->method('findById')->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->uc()->execute('dlb 12/24', 'imprimir');
    }

    // ---- cabecera ---------------------------------------------------------------------------------------------

    public function test_devuelve_los_datos_del_acta_y_de_la_asignatura(): void
    {
        $this->escenarioBase();

        $out = $this->uc()->execute('dlb 12/24', 'imprimir');

        $this->assertSame('dlb 12/24', $out['acta']);
        $this->assertSame([1101, 3001], [$out['id_asignatura'], $out['id_activ']]);
        $this->assertSame(['2', '15', '3', 'Aula 1', 'nota'], [$out['libro'], $out['pagina'], $out['linea'], $out['lugar'], $out['observ']]);
        $this->assertSame('Lóg', $out['nombre_corto']);
        $this->assertSame('Lógica', html_entity_decode($out['nombre_asignatura'], ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'los acentos salen como entidades HTML latinas');
        $this->assertSame('Region Latina', $out['region_latin']);
        $this->assertStringStartsWith('Stgr', $out['reg_stgr']);
        $this->assertNotSame('', $out['nombre_prelatura']);
    }

    /**
     * @dataProvider aniosProvider
     */
    public function test_el_curso_se_muestra_en_numeros_romanos(?int $year, string $esperado): void
    {
        $this->escenarioBase($this->asignatura($year));

        $this->assertSame($esperado, $this->uc()->execute('dlb 12/24', 'imprimir')['any']);
    }

    /**
     * @return iterable<string, array{?int, string}>
     */
    public static function aniosProvider(): iterable
    {
        yield '1' => [1, 'I'];
        yield '2' => [2, 'II'];
        yield '3' => [3, 'III'];
        yield '4' => [4, 'IV'];
        yield '5' => [5, ''];
        yield '0' => [0, ''];
        yield 'sin año' => [null, ''];
    }

    // ---- alumnos y paginación ------------------------------------------------------------------------------------

    public function test_los_alumnos_salen_ordenados_sin_tener_en_cuenta_los_acentos(): void
    {
        $this->escenarioBase();
        $this->conAlumnos(
            [$this->nota(1, 'sobresaliente'), $this->nota(2, 'notable'), $this->nota(3, 'aprobado')],
            [1 => $this->persona('ÁLVAREZ, Luis'), 2 => $this->persona('BAEZA, Ana'), 3 => $this->persona('AGUILAR, Eva')]
        );

        $out = $this->uc()->execute('dlb 12/24', 'imprimir');

        $this->assertSame(['AGUILAR, Eva', 'ÁLVAREZ, Luis', 'BAEZA, Ana'], array_column($out['aPersonasNotas_list'], 'nom'));
        $this->assertSame(['aprobado', 'sobresaliente', 'notable'], array_column($out['aPersonasNotas_list'], 'nota'));
        $this->assertSame(3, $out['num_alumnos']);
    }

    public function test_un_alumno_que_no_se_encuentra_se_avisa_y_no_aparece(): void
    {
        $this->escenarioBase();
        $this->conAlumnos([$this->nota(1, 'notable'), $this->nota(2, 'aprobado')], [1 => $this->persona('PEREZ, Ana')]);

        $out = $this->uc()->execute('dlb 12/24', 'imprimir');

        $this->assertSame(1, $out['num_alumnos']);
        $this->assertStringContainsString('id_nom = 2', $out['errores']);
    }

    public function test_sin_errores_el_texto_de_errores_es_vacio(): void
    {
        $this->escenarioBase();

        $this->assertSame('', $this->uc()->execute('dlb 12/24', 'imprimir')['errores']);
    }

    /**
     * @dataProvider paginacionProvider
     */
    public function test_reparto_de_alumnos_entre_las_dos_caras(string $modo, int $alumnos, int $maxCaraA, int $caraA, int $caraB): void
    {
        $this->escenarioBase();
        $notas = [];
        $personas = [];
        for ($i = 1; $i <= $alumnos; $i++) {
            $notas[] = $this->nota($i, 'aprobado');
            $personas[$i] = $this->persona(sprintf('ALUMNO %03d, X', $i));
        }
        $this->conAlumnos($notas, $personas);

        $out = $this->uc()->execute('dlb 12/24', $modo);

        $this->assertSame($maxCaraA, $out['lin_max_cara_A']);
        $this->assertSame($caraA, $out['alum_cara_A']);
        $this->assertSame($caraB, $out['alum_cara_B']);
    }

    /**
     * @return iterable<string, array{string, int, int, int, int}>
     */
    public static function paginacionProvider(): iterable
    {
        // imprimir: 45 líneas por A4 → 45 - 16 - 2 = 27 por la cara A; resto: 42 → 24.
        yield 'imprimir, caben en la cara A' => ['imprimir', 10, 27, 10, 0];
        yield 'imprimir, justo el máximo' => ['imprimir', 27, 27, 27, 0];
        yield 'imprimir, uno de más pasa a la cara B' => ['imprimir', 28, 27, 27, 1];
        yield 'otro modo, máximo menor' => ['ver', 25, 24, 24, 1];
        yield 'sin alumnos' => ['imprimir', 0, 27, 0, 0];
    }

    // ---- tribunal ---------------------------------------------------------------------------------------------------

    private function tribunal(string $examinador): ActaTribunal
    {
        $t = new ActaTribunal();
        $t->setExaminador($examinador);

        return $t;
    }

    public function test_al_imprimir_en_una_dl_el_tribunal_sale_del_repositorio_de_la_dl(): void
    {
        $this->escenarioBase();
        $this->tribunalDl = $this->createMock(ActaTribunalDlRepositoryInterface::class);
        $this->tribunalDl->method('matching')->willReturn([$this->tribunal('Perez'), $this->tribunal('Gil')]);
        $this->tribunalComun = $this->createMock(ActaTribunalRepositoryInterface::class);
        $this->tribunalComun->expects($this->never())->method('matching');

        $out = $this->uc()->execute('dlb 12/24', 'imprimir');

        $this->assertSame(['Perez', 'Gil'], $out['examinadores']);
        $this->assertSame(4 + 2 * 2, $out['lin_tribunal']);
    }

    public function test_en_region_stgr_o_en_otro_modo_el_tribunal_sale_del_repositorio_comun(): void
    {
        $this->escenarioBase();
        $this->tribunalComun = $this->createMock(ActaTribunalRepositoryInterface::class);
        $this->tribunalComun->expects($this->exactly(2))->method('matching')->willReturn([$this->tribunal('Comun')]);
        $this->tribunalDl = $this->createMock(ActaTribunalDlRepositoryInterface::class);
        $this->tribunalDl->expects($this->never())->method('matching');

        $this->assertSame(['Comun'], $this->uc()->execute('dlb 12/24', 'ver')['examinadores']);
        $this->conAmbito('rstgr');
        $this->assertSame(['Comun'], $this->uc()->execute('dlb 12/24', 'imprimir')['examinadores']);
    }

    public function test_el_html_del_tribunal_incluye_examinadores_lugar_y_fecha_en_latin(): void
    {
        $this->escenarioBase();
        $this->tribunalDl = $this->createMock(ActaTribunalDlRepositoryInterface::class);
        $this->tribunalDl->method('matching')->willReturn([$this->tribunal('Perez')]);

        $out = $this->uc()->execute('dlb 12/24', 'imprimir');

        $this->assertStringContainsString('<div class="examinador">Perez</div>', $out['tribunal_html']);
        $this->assertStringContainsString('TRIBUNAL:', $out['tribunal_html']);
        $this->assertStringStartsWith('Aula 1,  ', $out['lugar_fecha']);
        $this->assertStringContainsString($out['lugar_fecha'], $out['tribunal_html']);
        $this->assertSame(6, $out['lin_tribunal'], '4 líneas de encabezado + 2 por examinador');
    }

    /**
     * Hallazgo: los nombres de los examinadores se insertan en el HTML del tribunal sin escapar.
     */
    public function test_el_nombre_del_examinador_no_se_escapa_en_el_html(): void
    {
        $this->escenarioBase();
        $this->tribunalDl = $this->createMock(ActaTribunalDlRepositoryInterface::class);
        $this->tribunalDl->method('matching')->willReturn([$this->tribunal('<b>Perez</b>')]);

        $out = $this->uc()->execute('dlb 12/24', 'imprimir');

        $this->assertStringContainsString('<div class="examinador"><b>Perez</b></div>', $out['tribunal_html']);
    }

    public function test_sin_fecha_el_lugar_va_con_la_fecha_vacia(): void
    {
        $acta = $this->createMock(Acta::class);
        $acta->method('getId_asignatura')->willReturn(1101);
        $acta->method('getLugar')->willReturn('Aula 1');
        $acta->method('getF_acta')->willReturn(null);
        $this->actas->method('findById')->willReturn($acta);
        $this->asignaturas->method('findById')->willReturn($this->asignatura());
        $this->tipos->method('findById')->willReturn($this->tipoLatin());

        $this->assertSame('Aula 1,  ', $this->uc()->execute('dlb 12/24', 'imprimir')['lugar_fecha']);
    }
}
