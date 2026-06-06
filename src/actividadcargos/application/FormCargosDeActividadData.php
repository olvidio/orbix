<?php

namespace src\actividadcargos\application;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;
use function src\shared\domain\helpers\is_true;

/**
 * Datos para `form_cargos_de_actividad`. Los desplegables se construyen en el front
 * ({@see \frontend\actividadcargos\helpers\FormCargosDeActividadHashCompose::withDesplegablesHtml}) a partir de `personas_select` / `cargos_select`.
 */
final class FormCargosDeActividadData
{
    public function __construct(
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
        private CargoRepositoryInterface $cargoRepository,
        private PersonaNRepositoryInterface $personaNRepository,
        private PersonaNaxRepositoryInterface $personaNaxRepository,
        private PersonaAgdRepositoryInterface $personaAgdRepository,
        private PersonaSRepositoryInterface $personaSRepository,
        private PersonaSSSCRepositoryInterface $personaSSSCRepository,
        private PersonaExRepositoryInterface $personaExRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    public function build(array $post): array
    {
        $Qid_item = '';
        $Qid_cargo = '';

        $Qpermiso = input_string($post, 'permiso');
        $Qid_dossier = input_int($post, 'id_dossier');
        if ($Qid_dossier <= 0) {
            $Qid_dossier = 3102;
        }

        $a_sel = input_string_list($post, 'sel');
        $Qid_schema = '';
        if ($a_sel !== []) {
            $Qid_nom = (int) strtok($a_sel[0], '#');
            $parsedItem = strtok('#');
            $Qid_item = '';
            if ($parsedItem !== false && is_numeric($parsedItem)) {
                $Qid_item = (int) $parsedItem;
            }
            strtok('#');
            $parsedSchema = strtok('#');
            $Qid_schema = $parsedSchema !== false ? (int) $parsedSchema : 0;
        } else {
            $Qid_nom = input_int($post, 'id_nom');
        }

        $Qmod = input_string($post, 'mod');
        $Qid_pau = input_int($post, 'id_pau');
        $Qobj_pau = input_string($post, 'obj_pau');

        $obj = 'ActividadCargo';

        $id_nom_real = '';
        $ape_nom = '';
        $observ = '';
        $puede_agd = '';
        $personas_select = null;

        if ($Qid_item !== '') {
            $cActividadCargo = $this->actividadCargoRepository->getActividadCargos([
                'id_item' => $Qid_item,
                'id_schema' => $Qid_schema,
            ]);
            $oActividadCargo = $cActividadCargo[0];
            $Qid_cargo = $oActividadCargo->getId_cargo();
            $Qid_nom = (int) ($oActividadCargo->getId_nom() ?? 0);
            $puede_agd = $oActividadCargo->isPuede_agd();
            $observ = (string) ($oActividadCargo->getObserv() ?? '');

            $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
            if (!is_object($oPersona)) {
                return ['error' => '<br>No encuentro a nadie con id_nom: ' . $Qid_nom . ' en ' . __FILE__];
            }
            $ape_nom = $oPersona->getPrefApellidosNombre();
            $id_nom_real = $Qid_nom;
        } else {
            if ($Qid_dossier === 3101) {
                $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
                if (!is_object($oPersona)) {
                    return ['error' => '<br>No encuentro a nadie con id_nom: ' . $Qid_nom . ' en ' . __FILE__];
                }
                $ape_nom = $oPersona->getPrefApellidosNombre();
                $id_nom_real = $Qid_nom;
            } elseif ($Qobj_pau !== '') {
                $obj_pau = strtok(urldecode($Qobj_pau), '&');
                strtok('&');
                $oOpciones = match ($obj_pau) {
                    'PersonaN' => $this->personaNRepository->getArrayPersonas(),
                    'PersonaNax' => $this->personaNaxRepository->getArrayPersonas(),
                    'PersonaAgd' => $this->personaAgdRepository->getArrayPersonas(),
                    'PersonaS' => $this->personaSRepository->getArrayPersonas(),
                    'PersonaSSSC' => $this->personaSSSCRepository->getArrayPersonas(),
                    'PersonaEx' => $this->resolvePersonaExOpciones($Qobj_pau),
                    default => [],
                };
                $personas_select = ['opciones' => $oOpciones];
            } else {
                return ['redir' => 'go_atras'];
            }
        }

        $cargos_select = [
            'opciones' => $this->cargoRepository->getArrayCargos(),
            'opcion_sel' => (string) $Qid_cargo,
        ];

        $chk = (!empty($puede_agd) && is_true($puede_agd)) ? 'checked' : '';

        $camposForm = 'id_cargo!observ';
        $camposNo = 'puede_agd';
        $a_camposHidden = [
            'id_item' => $Qid_item,
            'id_activ' => $Qid_pau,
            'mod' => $Qmod,
            'obj_pau' => $Qobj_pau,
            'permiso' => $Qpermiso,
        ];
        if ($id_nom_real !== '') {
            $a_camposHidden['id_nom'] = $id_nom_real;
        } else {
            if ($Qmod === 'nuevo') {
                $camposNo .= '!asis';
                $camposForm .= '!asis_presente';
            }
            $camposForm .= '!id_nom';
        }

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $url_cargo_nuevo = $web . '/src/actividadcargos/cargo_nuevo';
        $url_cargo_editar = $web . '/src/actividadcargos/cargo_editar';

        $out = [
            'obj' => $obj,
            'id_nom_real' => $id_nom_real,
            'ape_nom' => $ape_nom,
            'observ' => $observ,
            'puede_agd' => $puede_agd,
            'chk' => $chk,
            'Qmod' => $Qmod,
            'Qid_pau' => $Qid_pau,
            'Qid_item' => $Qid_item,
            'Qobj_pau' => $Qobj_pau,
            'Qid_schema' => $Qid_schema,
            'Qid_nom' => $Qid_nom,
            'id_dossier' => $Qid_dossier,
            'show_person_desplegable' => $id_nom_real === '',
            'show_asis' => $Qmod === 'nuevo' && $id_nom_real === '',
            'cargos_select' => $cargos_select,
            'hash_form_config' => [
                'campos_form' => $camposForm,
                'campos_no' => $camposNo,
                'campos_hidden' => $a_camposHidden,
            ],
            'url_cargo_nuevo' => $url_cargo_nuevo,
            'url_cargo_editar' => $url_cargo_editar,
        ];
        if ($personas_select !== null) {
            $out['personas_select'] = $personas_select;
        }

        return $out;
    }

    /**
     * @return array<int|string, string>
     */
    private function resolvePersonaExOpciones(string $qobjPau): array
    {
        $dec = urldecode($qobjPau);
        strtok($dec, '&');
        $tail = strtok('&');
        if ($tail === false) {
            return [];
        }
        $eqPos = strpos($tail, '=');
        $na_val = 'p' . ($eqPos !== false ? substr($tail, $eqPos + 1) : '');

        return $this->personaExRepository->getArrayPersonas($na_val);
    }
}
