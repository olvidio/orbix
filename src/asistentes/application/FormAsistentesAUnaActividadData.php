<?php

namespace src\asistentes\application;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;
use function src\shared\domain\helpers\is_true;

/**
 * Dossier asistentes a una actividad (3101). Datos puros; la UI vive en
 * {@see \frontend\asistentes\helpers\FormAsistentesAUnaActividadRender}.
 */
final class FormAsistentesAUnaActividadData
{
    public function __construct(
        private AsistenteRepositoryInterface $asistenteRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private PersonaNRepositoryInterface $personaNRepository,
        private PersonaNaxRepositoryInterface $personaNaxRepository,
        private PersonaAgdRepositoryInterface $personaAgdRepository,
        private PersonaSRepositoryInterface $personaSRepository,
        private PersonaExRepositoryInterface $personaExRepository,
        private ResumenPlazasService $resumenPlazasService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function build(array $input): array
    {
        $a_sel = (array)($input['sel'] ?? []);
        if (!empty($a_sel)) {
            $sel0 = $a_sel[0];
            $selKey = is_string($sel0) ? $sel0 : (is_scalar($sel0) ? (string)$sel0 : '');
            $Qid_nom = (int)strtok($selKey, '#');
        } else {
            $Qid_nom = input_int($input, 'id_nom', 0);
        }

        $Qid_activ = input_int($input, 'id_activ', 0);
        $Qid_pau = input_int($input, 'id_pau', 0);
        $Qobj_pau = input_string($input, 'obj_pau');
        if ($Qid_activ === 0) {
            $Qid_activ = $Qid_pau;
        }

        $obj = 'asistentes\\model\\entity\\Asistente';

        $this->actividadAllRepository->findById($Qid_activ);

        $personas_opciones = null;
        $personas_onchange = null;

        $obj_pau = $Qobj_pau;
        $oPersona = null;
        $id_nom_real = '';
        $ape_nom = '';
        $propio = 't';
        $falta = 'f';
        $est_ok = 'f';
        $observ = '';
        $observ_est = '';
        $plaza = PlazaId::PEDIDA;
        $propietario = '';

        if ($Qid_nom !== 0) {
            $mod = 'editar';
            $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
            if (!is_object($oPersona)) {
                return [
                    'error' => "<br>No encuentro a nadie con id_nom: $Qid_nom en  " . __FILE__ . ': line ' . __LINE__,
                ];
            }
            $id_tabla = $oPersona->getId_tabla();
            switch ($id_tabla) {
                case 'n':
                    $obj_pau = 'PersonaN';
                    break;
                case 'a':
                    $obj_pau = 'PersonaAgd';
                    break;
                case 's':
                    $obj_pau = 'PersonaS';
                    break;
                case 'nax':
                    $obj_pau = 'PersonaNax';
                    break;
                case 'sssc':
                    $obj_pau = 'PersonaSSSC';
                    break;
                case 'pn':
                case 'pa':
                    $obj_pau = 'PersonaEx';
                    break;
            }
            $ape_nom = $oPersona->getPrefApellidosNombre();
            $id_nom_real = (string)$Qid_nom;

            $cAsistentes = $this->asistenteRepository->getAsistentes(['id_activ' => $Qid_activ, 'id_nom' => $Qid_nom]);
            if ($cAsistentes === []) {
                return ['error' => _('No se encontró el asistente para esta actividad.')];
            }
            $oAsistente = $cAsistentes[0];
            $propio = $oAsistente->isPropio();
            $falta = $oAsistente->isFalta();
            $est_ok = $oAsistente->isEst_ok();
            $observ = $oAsistente->getObserv();
            $observ_est = $oAsistente->getObserv_est();
            $plaza = $oAsistente->getPlaza();
            $propietario = $oAsistente->getPropietario();

            if (ConfigGlobal::is_app_installed('actividadplazas') && $propietario !== '') {
                $parts = explode('>', (string)$propietario, 2);
                $child = (string)($parts[1] ?? '');
                if ($obj_pau !== 'PersonaEx' && $child !== '' && $child !== ConfigGlobal::mi_delef()) {
                    return [
                        'error' => sprintf(
                            _('los datos de asistencia los modifica el propietario de la plaza: %s'),
                            $child
                        ),
                    ];
                }
            }
        } else {
            $mod = 'nuevo';
            $obj_pau = $Qobj_pau !== '' ? urldecode($Qobj_pau) : '';
            $Qna = input_string($input, 'na');
            $na_val = 'p' . $Qna;

            switch ($obj_pau) {
                case 'PersonaN':
                    $personas_opciones = $this->personaNRepository->getArrayPersonas();
                    break;
                case 'PersonaNax':
                    $personas_opciones = $this->personaNaxRepository->getArrayPersonas();
                    break;
                case 'PersonaAgd':
                    $personas_opciones = $this->personaAgdRepository->getArrayPersonas();
                    break;
                case 'PersonaS':
                    $personas_opciones = $this->personaSRepository->getArrayPersonas();
                    break;
                case 'PersonaSSSC':
                case 'PersonaEx':
                    $personas_opciones = $this->personaExRepository->getArrayPersonas($na_val);
                    $obj_pau = 'PersonaEx';
                    break;
                default:
                    $personas_opciones = [];
            }
            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                $personas_onchange = 'fnjs_cmb_propietario()';
            }
        }

        $propio_chk = (!empty($propio) && is_true($propio)) ? 'checked' : '';
        $falta_chk = (!empty($falta) && is_true($falta)) ? 'checked' : '';
        $est_chk = (!empty($est_ok) && is_true($est_ok)) ? 'checked' : '';

        $plazas_installed = ConfigGlobal::is_app_installed('actividadplazas');
        $plaza_opciones = [];
        $propietario_opciones = [];
        $propietario_select_blanco = false;

        if ($plazas_installed) {
            $plaza_opciones = PlazaId::getArrayPosiblesPlazas();

            $dl_de_paso = false;
            if ($obj_pau === 'PersonaEx' && $Qid_nom !== 0) {
                $dlPersona = $oPersona->getDl();
                $dl_de_paso = $dlPersona !== null && $dlPersona !== '' ? $dlPersona : false;
            }
            $this->resumenPlazasService->setId_activ($Qid_activ);
            $propietario_opciones = $this->resumenPlazasService->getPosiblesPropietariosOpciones($dl_de_paso);
            $propietario_select_blanco = true;
        }

        $camposForm = 'observ!observ_est';
        if ($plazas_installed) {
            $camposForm .= '!plaza!propietario';
        }
        $a_camposHidden = [
            'id_activ' => $Qid_activ,
            'obj_pau' => $obj_pau,
            'mod' => $mod,
            'actualizar' => 0,
        ];
        if ($id_nom_real !== '') {
            $a_camposHidden['id_nom'] = (int)$id_nom_real;
        } else {
            $camposForm .= '!id_nom';
        }

        $out = [
            'obj' => $obj,
            'id_activ' => $Qid_activ,
            'id_nom_real' => $id_nom_real,
            'ape_nom' => $ape_nom,
            'propio_chk' => $propio_chk,
            'falta_chk' => $falta_chk,
            'est_chk' => $est_chk,
            'observ' => $observ,
            'observ_est' => $observ_est,
            'plazas_installed' => $plazas_installed,
            'hash_main' => [
                'campos_form' => $camposForm,
                'campos_no' => 'actualizar!id_nom!propio!falta!est_ok',
                'campos_hidden' => $a_camposHidden,
            ],
            'paths' => [
                'asistente_guardar' => 'src/asistentes/asistente_guardar',
                'form_self' => 'frontend/asistentes/controller/form_asistentes_a_una_actividad.php',
                'posibles_propietarios_data' => 'src/actividadplazas/posibles_propietarios_data',
            ],
            'plaza_opciones' => $plaza_opciones,
            'plaza_selected' => (string)$plaza,
            'propietario_opciones' => $propietario_opciones,
            'propietario_selected' => (string)$propietario,
            'propietario_select_blanco' => $propietario_select_blanco,
        ];

        if ($personas_opciones !== null) {
            $out['personas_opciones'] = $personas_opciones;
            $out['personas_onchange'] = $personas_onchange;
        }

        if ($plazas_installed) {
            $out['ajax_propietarios'] = [
                'path' => 'src/actividadplazas/posibles_propietarios_data',
                'campos_form' => 'id_activ!id_nom',
            ];
        }

        return $out;
    }
}
