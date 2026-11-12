<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\application\FormCargosDeActividadData;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\DescribeCriteria;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Formulario de cargo desde la actividad: edición desde la lista 3102 (sel),
 * alta desde la lista de asistentes 3101 (persona fija), personas de paso y
 * desplegables por tipo de persona.
 */
final class FormCargosDeActividadDataEdicionTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private ActividadCargoRepositoryInterface&MockObject $cargos;
    private PersonaExRepositoryInterface&MockObject $personasEx;
    private PersonaAgdRepositoryInterface&MockObject $personasAgd;
    private ?PersonaDl $persona = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->cargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $this->personasEx = $this->createMock(PersonaExRepositoryInterface::class);
        $this->personasAgd = $this->createMock(PersonaAgdRepositoryInterface::class);
        $this->persona = new PersonaDl();
        $this->persona->setNom('Juan');
        $this->persona->setApellido1('García');
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(fn (): ?PersonaDl => $this->persona);
        $this->sustituirServicio(PersonaFinderService::class, $finder);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        parent::tearDown();
    }

    private function uc(): FormCargosDeActividadData
    {
        $cargosCat = $this->createMock(CargoRepositoryInterface::class);
        $cargosCat->method('getArrayCargos')->willReturn([35 => 'sacd', 5 => 'director']);
        return new FormCargosDeActividadData(
            $this->cargos, $cargosCat,
            $this->createMock(PersonaNRepositoryInterface::class), $this->createMock(PersonaNaxRepositoryInterface::class),
            $this->personasAgd, $this->createMock(PersonaSRepositoryInterface::class),
            $this->createMock(PersonaSSSCRepositoryInterface::class), $this->personasEx,
        );
    }

    public function test_edicion_desde_la_lista_carga_el_cargo(): void
    {
        $c = new ActividadCargo();
        $c->setId_item(77);
        $c->setId_activ(3001);
        $c->setId_cargo(5);
        $c->setId_nom(100123);
        $c->setPuede_agd(true);
        $c->setObserv('obs');
        $this->cargos->method('matching')->willReturnCallback(function ($cr) use ($c): array {
            $this->assertSame(['id_item eq 77', 'id_schema eq 1001'], DescribeCriteria::filtros($cr));
            return [$c];
        });

        $out = $this->uc()->build(['sel' => ['100123#77#1#1001'], 'mod' => 'editar', 'id_pau' => '3001']);

        $this->assertSame([77, 1001, 100123, 'obs', 'checked'], [$out['Qid_item'], $out['Qid_schema'], $out['id_nom_real'], $out['observ'], $out['chk']]);
        $this->assertStringContainsString('García', $out['ape_nom']);
        $this->assertSame('5', $out['cargos_select']['opcion_sel']);
        $this->assertFalse($out['show_person_desplegable']);
        $hidden = $out['hash_form_config']['campos_hidden'];
        $this->assertSame(['id_item' => 77, 'id_activ' => 3001, 'id_nom' => 100123], HashB::open($hidden['ctx_guardar'], 'cargo_editar'));
        $this->assertSame(100123, $hidden['id_nom']);
    }

    public function test_edicion_de_un_cargo_que_ya_no_existe(): void
    {
        $this->cargos->method('matching')->willReturn([]);
        $this->assertSame(['error' => 'no encuentro el cargo'], $this->uc()->build(['sel' => ['100123#77#1#1001'], 'mod' => 'editar']));
    }

    public function test_edicion_con_persona_inexistente(): void
    {
        $c = new ActividadCargo();
        $c->setId_item(77);
        $c->setId_activ(3001);
        $c->setId_cargo(5);
        $c->setId_nom(100123);
        $this->cargos->method('matching')->willReturn([$c]);
        $this->persona = null;
        $this->assertStringContainsString('No encuentro a nadie con id_nom: 100123', $this->uc()->build(['sel' => ['100123#77#1#1001']])['error']);
    }

    public function test_alta_desde_la_lista_de_asistentes_fija_la_persona(): void
    {
        $out = $this->uc()->build(['id_dossier' => '3101', 'id_nom' => '100123', 'id_pau' => '3001', 'mod' => 'nuevo']);
        $this->assertSame(100123, $out['id_nom_real']);
        $this->assertFalse($out['show_asis'], 'la persona ya asiste');
        $this->assertSame(['id_activ' => 3001, 'id_nom' => 100123], HashB::open($out['hash_form_config']['campos_hidden']['ctx_guardar'], 'cargo_nuevo'));

        $this->persona = null;
        $this->assertStringContainsString('No encuentro a nadie', $this->uc()->build(['id_dossier' => '3101', 'id_nom' => '1'])['error']);
    }

    public function test_alta_con_personas_de_paso_y_de_agd(): void
    {
        $this->personasEx->expects($this->once())->method('getArrayPersonas')->with('pn')->willReturn([-5 => 'De paso']);
        $out = $this->uc()->build(['obj_pau' => 'PersonaEx&na=n', 'mod' => 'nuevo', 'id_pau' => '3001']);
        $this->assertSame([-5 => 'De paso'], $out['personas_select']['opciones']);
        $this->assertTrue($out['show_asis']);

        $this->personasAgd->method('getArrayPersonas')->willReturn([7 => 'Agd']);
        $this->assertSame([7 => 'Agd'], $this->uc()->build(['obj_pau' => urlencode('PersonaAgd'), 'mod' => 'nuevo'])['personas_select']['opciones']);
        $this->assertSame([], $this->uc()->build(['obj_pau' => 'PersonaEx', 'mod' => 'nuevo'])['personas_select']['opciones'], 'PersonaEx sin na');
        $this->assertSame([], $this->uc()->build(['obj_pau' => 'Otra', 'mod' => 'nuevo'])['personas_select']['opciones']);
    }
}
