<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\application\FormCargosDeActividadData;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\shared\config\ConfigGlobal;

/**
 * Payload JSON de {@see FormCargosDeActividadData::build} (formulario cargos en actividad, dossier 3102).
 */
final class FormCargosDeActividadDataTest extends TestCase
{
    public function test_build_sin_item_ni_dossier_3101_ni_obj_pau_devuelve_redir(): void
    {
        $builder = $this->makeBuilder();
        $data = $builder->build([
            'id_dossier' => 3102,
            'id_nom' => 0,
        ]);

        $this->assertSame(['redir' => 'go_atras'], $data);
    }

    public function test_build_con_obj_pau_persona_n_incluye_desplegables_y_hash(): void
    {
        $personaN = $this->createMock(PersonaNRepositoryInterface::class);
        $personaN->method('getArrayPersonas')->willReturn([10 => 'Uno']);
        $cargo = $this->createMock(CargoRepositoryInterface::class);
        $cargo->method('getArrayCargos')->willReturn([5 => 'Cargo A']);
        $builder = $this->makeBuilder(personaN: $personaN, cargo: $cargo);

        $data = $builder->build([
            'obj_pau' => 'PersonaN',
            'mod' => 'nuevo',
            'pau' => 'pau1',
            'id_pau' => 7,
            'permiso' => 'x',
            'id_dossier' => 3102,
        ]);

        $this->assertSame(['opciones' => [10 => 'Uno']], $data['personas_select']);
        $this->assertSame(
            ['opciones' => [5 => 'Cargo A'], 'opcion_sel' => ''],
            $data['cargos_select']
        );
        $this->assertSame(
            [
                'campos_form' => 'id_cargo!observ!asis_presente!id_nom',
                'campos_no' => 'puede_agd!asis',
                'campos_hidden' => [
                    'id_item' => '',
                    'id_activ' => 7,
                    'mod' => 'nuevo',
                    'obj_pau' => 'PersonaN',
                    'permiso' => 'x',
                ],
            ],
            $data['hash_form_config']
        );

        $base = rtrim(ConfigGlobal::getWeb(), '/');
        $this->assertSame($base . '/src/actividadcargos/cargo_nuevo', $data['url_cargo_nuevo']);
        $this->assertSame($base . '/src/actividadcargos/cargo_editar', $data['url_cargo_editar']);
    }

    public function test_build_cumple_contrato_de_claves_con_obj_pau(): void
    {
        $personaN = $this->createMock(PersonaNRepositoryInterface::class);
        $personaN->method('getArrayPersonas')->willReturn([]);
        $cargo = $this->createMock(CargoRepositoryInterface::class);
        $cargo->method('getArrayCargos')->willReturn([]);
        $builder = $this->makeBuilder(personaN: $personaN, cargo: $cargo);

        $data = $builder->build([
            'obj_pau' => 'PersonaN',
            'mod' => 'nuevo',
            'id_pau' => 0,
            'permiso' => '',
            'id_dossier' => 3102,
        ]);

        $expectedKeys = [
            'obj',
            'id_nom_real',
            'ape_nom',
            'observ',
            'puede_agd',
            'chk',
            'Qmod',
            'Qid_pau',
            'Qid_item',
            'Qobj_pau',
            'Qid_schema',
            'Qid_nom',
            'id_dossier',
            'show_person_desplegable',
            'show_asis',
            'cargos_select',
            'hash_form_config',
            'url_cargo_nuevo',
            'url_cargo_editar',
            'personas_select',
        ];
        $this->assertSame($expectedKeys, array_keys($data), 'Contrato de claves del payload (rama obj_pau).');

        $this->assertSame('ActividadCargo', $data['obj']);
        $this->assertTrue($data['show_person_desplegable']);
        $this->assertTrue($data['show_asis']);
        $this->assertIsString($data['url_cargo_nuevo']);
        $this->assertIsString($data['url_cargo_editar']);
    }

    private function makeBuilder(
        ?object $personaN = null,
        ?object $cargo = null,
    ): FormCargosDeActividadData {
        $cargoRepo = $cargo ?? $this->createMock(CargoRepositoryInterface::class);
        if ($cargo === null) {
            $cargoRepo->method('getArrayCargos')->willReturn([]);
        }

        $personaNRepo = $personaN ?? $this->createMock(PersonaNRepositoryInterface::class);
        if ($personaN === null) {
            $personaNRepo->method('getArrayPersonas')->willReturn([]);
        }

        $personaNax = $this->createMock(PersonaNaxRepositoryInterface::class);
        $personaNax->method('getArrayPersonas')->willReturn([]);
        $personaAgd = $this->createMock(PersonaAgdRepositoryInterface::class);
        $personaAgd->method('getArrayPersonas')->willReturn([]);
        $personaS = $this->createMock(PersonaSRepositoryInterface::class);
        $personaS->method('getArrayPersonas')->willReturn([]);
        $personaSssc = $this->createMock(PersonaSSSCRepositoryInterface::class);
        $personaSssc->method('getArrayPersonas')->willReturn([]);
        $personaEx = $this->createMock(PersonaExRepositoryInterface::class);
        $personaEx->method('getArrayPersonas')->willReturn([]);

        return new FormCargosDeActividadData(
            $this->createMock(ActividadCargoRepositoryInterface::class),
            $cargoRepo,
            $personaNRepo,
            $personaNax,
            $personaAgd,
            $personaS,
            $personaSssc,
            $personaEx,
        );
    }
}
