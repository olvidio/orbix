<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\ExpedienteNotasPersona;
use src\notas\application\NotasDeUnaPersonaData;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\Select_notas_de_una_persona;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use Tests\myTest;

/** Segmento del dossier 1011: cabeceras, botones, enlace de alta según permiso y valores de la lista. */
final class SelectNotasDeUnaPersonaTest extends myTest
{
    private function widget(): Select_notas_de_una_persona
    {
        $matriculas = $this->createMock(MatriculaRepositoryInterface::class);
        $matriculas->method('getMatriculasPendientes')->willReturn([]);
        $notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notas->method('matching')->willReturn([]);
        $datos = new NotasDeUnaPersonaData(
            $matriculas,
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(AsignaturaRepositoryInterface::class),
            new ExpedienteNotasPersona($notas),
            new PlanEstudiosDePersona($notas)
        );
        $w = new Select_notas_de_una_persona($datos);
        $w->setPau('p');
        $w->setObj_pau('PersonaN');
        $w->setId_pau('10011');
        $w->setId_dossier(1011);
        $w->setBloque('bloque1');
        $w->setQueSel('sel');

        return $w;
    }

    public function test_el_segmento_declara_su_tipo_cabeceras_botones_y_ruta_de_borrado(): void
    {
        $w = $this->widget();
        $w->setPermiso(1);

        $s = $w->getSegmentData();

        $this->assertSame('select_notas_de_una_persona', $s['segment_tipo']);
        $this->assertSame('bloque1', $s['bloque']);
        $this->assertCount(8, $s['tabla']['cabeceras']);
        $this->assertSame('fecha', $s['tabla']['cabeceras'][3]['class']);
        $this->assertCount(2, $s['tabla']['botones']);
        $this->assertSame('src/notas/persona_nota_eliminar', $s['paths']['persona_nota_eliminar']);
        $this->assertSame('', $s['aviso']);
    }

    public function test_los_campos_ocultos_del_hash_llevan_la_persona_y_el_permiso(): void
    {
        $w = $this->widget();
        $w->setPermiso('3');

        $hidden = $w->getSegmentData()['hash_main']['campos_hidden'];

        $this->assertSame(['pau' => 'p', 'id_pau' => 10011, 'obj_pau' => 'PersonaN', 'queSel' => 'sel', 'id_dossier' => '1011', 'permiso' => 3], $hidden);
    }

    /**
     * @dataProvider permisosProvider
     */
    public function test_el_enlace_de_alta_solo_existe_con_permiso_3(int|string $permiso, bool $hayEnlace): void
    {
        $w = $this->widget();
        $w->setPermiso($permiso);

        $spec = $w->getSegmentData()['link_insert_spec'];

        if (!$hayEnlace) {
            $this->assertNull($spec);

            return;
        }
        $this->assertSame('frontend/notas/controller/form_notas_de_una_persona.php', $spec['path']);
        $this->assertSame(['mod' => 'nuevo', 'pau' => 'p', 'id_pau' => 10011, 'obj_pau' => 'PersonaN', 'id_dossier' => 1011], $spec['query']);
    }

    /**
     * @return iterable<string, array{int|string, bool}>
     */
    public static function permisosProvider(): iterable
    {
        yield 'sin permiso' => [0, false];
        yield 'lectura' => [1, false];
        yield 'permiso 2' => [2, false];
        yield 'permiso 3' => [3, true];
        yield 'permiso 3 como texto' => ['3', true];
    }

    public function test_sin_notas_la_tabla_no_lleva_seleccion_ni_scroll(): void
    {
        $w = $this->widget();
        $w->setPermiso(3);
        $w->setQid_sel('1101#1101#1');
        $w->setQscroll_id(250);

        $s = $w->getSegmentData();

        $this->assertSame([], $s['tabla']['valores']);
        $this->assertSame('1101#1101#1', $s['id_sel_value']);
    }
}
