<?php

namespace src\actividadcargos\application;

use src\shared\config\ConfigGlobal;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\Persona;
use function src\shared\domain\helpers\is_true;

/**
 * Datos para `form_cargos_de_actividad`. Los desplegables se construyen en el front
 * ({@see \frontend\actividadcargos\helpers\FormCargosDeActividadHashCompose::withDesplegablesHtml}) a partir de `personas_select` / `cargos_select`.
 */
final class FormCargosDeActividadData
{
    /**
     * @return array{error?: string, redir?: string, obj: string, id_nom_real: int|string, ape_nom: string, observ: string, puede_agd: mixed, chk: string, Qmod: string, id_dossier: int, personas_select?: array{opciones: array<int|string, string>, opcion_sel?: string}|null, cargos_select: array{opciones: array<int|string, string>, opcion_sel: string}, hash_form_config: array{campos_form: string, campos_no: string, campos_hidden: array<string, mixed>}, url_cargo_nuevo: string, url_cargo_editar: string, show_person_desplegable: bool, show_asis: bool, Qid_pau: int, Qid_item: int|string, Qobj_pau: string, Qid_schema: int|string, Qid_nom: int}
     */
    public static function build(array $post): array
    {
        $Qid_item = '';
        $Qid_cargo = '';

        $Qpermiso = (string)($post['permiso'] ?? '');
        $Qid_dossier = (int)($post['id_dossier'] ?? 0);
        if ($Qid_dossier <= 0) {
            $Qid_dossier = 3102;
        }

        $a_sel = isset($post['sel']) ? (array)$post['sel'] : [];
        $Qid_schema = '';
        if (!empty($a_sel)) {
            $Qid_nom = (int)strtok($a_sel[0], '#');
            $Qid_item = (int)strtok('#');
            $Qid_item = empty($Qid_item) ? '' : $Qid_item;
            strtok('#');
            $Qid_schema = (int)strtok('#');
        } else {
            $Qid_nom = (int)($post['id_nom'] ?? 0);
        }

        $Qmod = (string)($post['mod'] ?? '');
        $pau = (string)($post['pau'] ?? '');
        $Qid_pau = (int)($post['id_pau'] ?? 0);
        $Qobj_pau = (string)($post['obj_pau'] ?? '');

        $obj = 'ActividadCargo';

        $id_nom_real = '';
        $ape_nom = '';
        $observ = '';
        $puede_agd = '';
        $personas_select = null;

        if (!empty($Qid_item)) {
            $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
            $cActividadCargo = $ActividadCargoRepository->getActividadCargos([
                'id_item' => $Qid_item,
                'id_schema' => $Qid_schema,
            ]);
            $oActividadCargo = $cActividadCargo[0];
            $Qid_cargo = $oActividadCargo->getId_cargo();
            $Qid_nom = $oActividadCargo->getId_nom();
            $puede_agd = $oActividadCargo->isPuede_agd();
            $observ = $oActividadCargo->getObserv();

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
            } elseif (!empty($Qobj_pau)) {
                $obj_pau = strtok(urldecode($Qobj_pau), '&');
                strtok('&');
                switch ($obj_pau) {
                    case 'PersonaN':
                        $oOpciones = $GLOBALS['container']->get(PersonaNRepositoryInterface::class)->getArrayPersonas();
                        break;
                    case 'PersonaNax':
                        $oOpciones = $GLOBALS['container']->get(PersonaNaxRepositoryInterface::class)->getArrayPersonas();
                        break;
                    case 'PersonaAgd':
                        $oOpciones = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class)->getArrayPersonas();
                        break;
                    case 'PersonaS':
                        $oOpciones = $GLOBALS['container']->get(PersonaSRepositoryInterface::class)->getArrayPersonas();
                        break;
                    case 'PersonaSSSC':
                        $oOpciones = $GLOBALS['container']->get(PersonaSSSCRepositoryInterface::class)->getArrayPersonas();
                        break;
                    case 'PersonaEx':
                        $dec = urldecode($Qobj_pau);
                        strtok($dec, '&');
                        $tail = strtok('&');
                        $na_val = 'p' . substr((string)$tail, (int)strpos((string)$tail, '=') + 1);
                        $oOpciones = $GLOBALS['container']->get(PersonaExRepositoryInterface::class)->getArrayPersonas($na_val);
                        break;
                    default:
                        $oOpciones = [];
                }
                $personas_select = ['opciones' => $oOpciones];
            } else {
                return ['redir' => 'go_atras'];
            }
        }

        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $cargos_select = [
            'opciones' => $CargoRepository->getArrayCargos(),
            'opcion_sel' => (string)$Qid_cargo,
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
        if (!empty($id_nom_real)) {
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
            'show_person_desplegable' => empty($id_nom_real),
            'show_asis' => $Qmod === 'nuevo' && empty($id_nom_real),
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
}
