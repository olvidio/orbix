<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadestudios\application\E43CertificadoData;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Casa;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;
use Tests\support\DescribeCriteria;

/** Certificado E43: cabecera de la persona y de la actividad y las asignaturas matriculadas con su nota y acta. */
final class E43CertificadoDataTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const ID_NOM = 10011;
    private const ID_ACTIV = 3001;

    private ActividadAllRepositoryInterface&MockObject $actividades;
    private MatriculaRepositoryInterface&MockObject $matriculas;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private PersonaNotaRepositoryInterface&MockObject $notas;
    private PersonaFinderService&MockObject $finder;
    /** @var list<string> */
    private array $filtrosMatriculas = [];
    /** @var list<list<string>> */
    private array $filtrosNotas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->finder = $this->createMock(PersonaFinderService::class);
        $this->sustituirServicio(PersonaFinderService::class, $this->finder);

        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->matriculas = $this->createMock(MatriculaRepositoryInterface::class);
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        parent::tearDown();
    }

    private function uc(): E43CertificadoData
    {
        return new E43CertificadoData($this->actividades, $this->matriculas, $this->asignaturas, $this->notas);
    }

    private function persona(?string $fNacimiento = '1990-05-20', ?string $dl = 'dlp'): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getNombreApellidos')->willReturn('Joan Pla Roca');
        $p->method('getLugar_nacimiento')->willReturn('Reus');
        $p->method('getF_nacimiento')->willReturn($fNacimiento === null ? null : new DateTimeLocal($fNacimiento));
        $p->method('getDl')->willReturn($dl);
        $this->finder->method('findPersonaEnGlobalODePaso')->with(self::ID_NOM)->willReturn($p);
    }

    private function actividad(?int $idUbi = null): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_ubi')->willReturn($idUbi);
        $a->method('getF_ini')->willReturn(new DateTimeLocal('2026-07-01'));
        $a->method('getF_fin')->willReturn(new DateTimeLocal('2026-07-25'));
        $this->actividades->method('findById')->with(self::ID_ACTIV)->willReturn($a);
    }

    /**
     * @param list<int> $idsAsignatura
     */
    private function matriculas(array $idsAsignatura): void
    {
        $lista = [];
        foreach ($idsAsignatura as $id) {
            $m = $this->createMock(Matricula::class);
            $m->method('getId_asignatura')->willReturn($id);
            $lista[] = $m;
        }
        $this->matriculas->method('matching')->willReturnCallback(function (Criteria $c) use ($lista): array {
            $this->filtrosMatriculas = DescribeCriteria::filtros($c);

            return $lista;
        });
    }

    private function asignatura(?string $nombreCorto): Asignatura&MockObject
    {
        $a = $this->createMock(Asignatura::class);
        $a->method('getNombre_corto')->willReturn($nombreCorto);

        return $a;
    }

    public function test_persona_no_encontrada_devuelve_el_error_y_el_esqueleto_vacio(): void
    {
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturn(null);
        $this->actividades->expects($this->never())->method('findById');

        $r = $this->uc()->execute(['id_nom' => self::ID_NOM, 'id_activ' => self::ID_ACTIV]);

        $this->assertStringContainsString('No encuentro a nadie con id_nom: ' . self::ID_NOM, $r['msg_err']);
        $this->assertSame('', $r['nom']);
        $this->assertSame(ConfigGlobal::mi_delef(), $r['dl_origen']);
        $this->assertSame('', $r['dl_destino']);
        $this->assertSame(0, $r['matriculas']);
        $this->assertSame([], $r['aAsignaturasMatriculadas']);
    }

    /** Hallazgo: el mensaje de error incluye la ruta del fichero PHP del servidor. */
    public function test_el_error_de_persona_no_encontrada_incluye_la_ruta_del_fichero(): void
    {
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturn(null);

        $r = $this->uc()->execute(['id_nom' => self::ID_NOM, 'id_activ' => self::ID_ACTIV]);

        $this->assertStringContainsString('E43CertificadoData.php', $r['msg_err']);
    }

    public function test_actividad_no_encontrada_devuelve_la_cabecera_de_la_persona_y_el_error(): void
    {
        $this->persona();
        $this->actividades->method('findById')->willReturn(null);
        $this->matriculas->expects($this->never())->method('matching');

        $r = $this->uc()->execute(['id_nom' => self::ID_NOM, 'id_activ' => self::ID_ACTIV]);

        $this->assertStringContainsString((string) self::ID_ACTIV, $r['msg_err']);
        $this->assertSame('Joan Pla Roca', $r['nom']);
        $this->assertSame('Reus (20/5/1990)', $r['txt_nacimiento']);
        $this->assertSame('dlp', $r['dl_destino']);
        $this->assertSame('', $r['txt_actividad']);
    }

    public function test_certificado_completo_con_nota_y_acta_de_cada_asignatura(): void
    {
        $this->persona();
        $this->actividad();
        $this->matriculas([1101, 1102]);
        $this->asignaturas->method('findById')->willReturnMap([
            [1101, $this->asignatura('Lógica')],
            [1102, $this->asignatura('Metafísica')],
        ]);
        $nota = $this->createMock(PersonaNota::class);
        $nota->method('getNota_txt')->willReturn('8,5/10');
        $nota->method('getActa')->willReturn('dlb 12/26');
        $nota->method('getF_acta')->willReturn(new DateTimeLocal('2026-07-24'));
        $otra = $this->createMock(PersonaNota::class);
        $otra->method('getNota_txt')->willReturn('no debe salir');
        $this->notas->method('matching')->willReturnCallback(function (Criteria $c) use ($nota, $otra): array {
            $f = DescribeCriteria::filtros($c);
            $this->filtrosNotas[] = $f;

            return $f[1] === 'id_asignatura eq 1101' ? [$nota, $otra] : [];
        });

        $r = $this->uc()->execute(['id_nom' => (string) self::ID_NOM, 'id_activ' => (string) self::ID_ACTIV]);

        $this->assertSame('', $r['msg_err']);
        $this->assertSame(['id_nom eq ' . self::ID_NOM, 'id_activ eq ' . self::ID_ACTIV], $this->filtrosMatriculas);
        $this->assertSame(['id_nom eq ' . self::ID_NOM, 'id_asignatura eq 1101'], $this->filtrosNotas[0]);
        $this->assertSame(2, $r['matriculas']);
        $this->assertSame([
            ['nom_asignatura' => 'Lógica', 'nota' => '8,5/10', 'f_acta' => '24/7/2026', 'acta' => 'dlb 12/26'],
            ['nom_asignatura' => 'Metafísica', 'nota' => '', 'f_acta' => '', 'acta' => ''],
        ], $r['aAsignaturasMatriculadas']);
        $this->assertSame(', 1/7/2026-25/7/2026', $r['txt_actividad']);
        $this->assertSame(ConfigGlobal::mi_delef(), $r['dl_origen']);
    }

    public function test_el_lugar_de_la_actividad_es_el_nombre_de_la_casa(): void
    {
        $this->persona();
        $this->actividad(1077);
        $this->matriculas([]);
        $centros = $this->createMock(CentroRepositoryInterface::class);
        $centros->method('findById')->willReturn(null);
        $this->sustituirServicio(CentroRepositoryInterface::class, $centros);
        $casa = $this->createMock(Casa::class);
        $casa->method('getNombre_ubi')->willReturn('Casa Alta');
        $casas = $this->createMock(CasaRepositoryInterface::class);
        $casas->method('findById')->with(1077)->willReturn($casa);
        $this->sustituirServicio(CasaRepositoryInterface::class, $casas);

        $r = $this->uc()->execute(['id_nom' => self::ID_NOM, 'id_activ' => self::ID_ACTIV]);

        $this->assertSame('Casa Alta, 1/7/2026-25/7/2026', $r['txt_actividad']);
    }

    public function test_un_lugar_que_ya_no_existe_deja_el_nombre_vacio(): void
    {
        $this->persona();
        $this->actividad(1077);
        $this->matriculas([]);
        $centros = $this->createMock(CentroRepositoryInterface::class);
        $centros->method('findById')->willReturn(null);
        $this->sustituirServicio(CentroRepositoryInterface::class, $centros);
        $casas = $this->createMock(CasaRepositoryInterface::class);
        $casas->method('findById')->willReturn(null);
        $this->sustituirServicio(CasaRepositoryInterface::class, $casas);

        $r = $this->uc()->execute(['id_nom' => self::ID_NOM, 'id_activ' => self::ID_ACTIV]);

        $this->assertSame(', 1/7/2026-25/7/2026', $r['txt_actividad']);
    }

    public function test_sin_matriculas_se_avisa(): void
    {
        $this->persona();
        $this->actividad();
        $this->matriculas([]);
        $this->asignaturas->expects($this->never())->method('findById');

        $r = $this->uc()->execute(['id_nom' => self::ID_NOM, 'id_activ' => self::ID_ACTIV]);

        $this->assertNotSame('', $r['msg_err']);
        $this->assertSame(0, $r['matriculas']);
        $this->assertSame([], $r['aAsignaturasMatriculadas']);
    }

    public function test_el_imprimible_anyade_una_fila_en_blanco_al_final_sin_contarla(): void
    {
        $this->persona();
        $this->actividad();
        $this->matriculas([1101]);
        $this->asignaturas->method('findById')->willReturn($this->asignatura('Lógica'));

        $r = $this->uc()->execute(['id_nom' => self::ID_NOM, 'id_activ' => self::ID_ACTIV, 'append_blank_footer' => '1']);

        $this->assertSame(1, $r['matriculas']);
        $this->assertCount(2, $r['aAsignaturasMatriculadas']);
        $this->assertSame(['nom_asignatura' => ' ', 'nota' => '', 'f_acta' => '', 'acta' => ''], $r['aAsignaturasMatriculadas'][1]);
    }

    public function test_una_asignatura_que_ya_no_existe_o_sin_nombre_corto_sale_con_el_nombre_vacio(): void
    {
        $this->persona();
        $this->actividad();
        $this->matriculas([1101, 1102]);
        $this->asignaturas->method('findById')->willReturnMap([[1101, null], [1102, $this->asignatura(null)]]);

        $r = $this->uc()->execute(['id_nom' => self::ID_NOM, 'id_activ' => self::ID_ACTIV]);

        $this->assertSame(['', ''], array_column($r['aAsignaturasMatriculadas'], 'nom_asignatura'));
    }

    public function test_una_nota_sin_acta_ni_fecha_deja_las_celdas_vacias(): void
    {
        $this->persona();
        $this->actividad();
        $this->matriculas([1101]);
        $nota = $this->createMock(PersonaNota::class);
        $nota->method('getNota_txt')->willReturn('Magna cum laude (8,6-9,5/10)');
        $nota->method('getActa')->willReturn(null);
        $nota->method('getF_acta')->willReturn(null);
        $this->notas->method('matching')->willReturn([$nota]);

        $fila = $this->uc()->execute(['id_nom' => self::ID_NOM, 'id_activ' => self::ID_ACTIV])['aAsignaturasMatriculadas'][0];

        $this->assertSame(['nom_asignatura' => '', 'nota' => 'Magna cum laude (8,6-9,5/10)', 'f_acta' => '', 'acta' => ''], $fila);
    }

    /**
     * Hallazgo: sin fecha ni lugar de nacimiento el texto queda « ()»; y sin dl de la persona, el destino vacío.
     */
    public function test_sin_datos_de_nacimiento_el_texto_queda_con_los_parentesis_vacios(): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getNombreApellidos')->willReturn('Joan Pla Roca');
        $p->method('getLugar_nacimiento')->willReturn(null);
        $p->method('getF_nacimiento')->willReturn(null);
        $p->method('getDl')->willReturn(null);
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturn($p);
        $this->actividad();
        $this->matriculas([]);

        $r = $this->uc()->execute(['id_nom' => self::ID_NOM, 'id_activ' => self::ID_ACTIV]);

        $this->assertSame(' ()', $r['txt_nacimiento']);
        $this->assertSame('', $r['dl_destino']);
    }
}
