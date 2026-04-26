<?php

namespace src\asistentes\application;

use frontend\shared\web\Desplegable;
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
use web\Hash;
use function src\shared\domain\helpers\is_true;

/**
 * Dossier asistentes a una actividad (3101).
 */
final class FormAsistentesAUnaActividadData
{
    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public static function build(array $input): array
    {
        $a_sel = (array)($input['sel'] ?? []);
        if (!empty($a_sel)) {
            $Qid_nom = (int)strtok($a_sel[0], '#');
        } else {
            $Qid_nom = (int)($input['id_nom'] ?? 0);
        }

        $Qid_activ = (int)($input['id_activ'] ?? 0);
        $Qid_pau = (int)($input['id_pau'] ?? 0);
        $Qobj_pau = (string)($input['obj_pau'] ?? '');
        if (empty($Qid_activ)) {
            $Qid_activ = $Qid_pau;
        }

        $AsistenteRepository = $GLOBALS['container']->get(AsistenteRepositoryInterface::class);
        $obj = 'asistentes\\model\\entity\\Asistente';

        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $ActividadAllRepository->findById($Qid_activ);

        $desplegable_personas_html = '';
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

        if (!empty($Qid_nom)) {
            $mod = 'editar';
            $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
            if (!is_object($oPersona)) {
                exit("<br>No encuentro a nadie con id_nom: $Qid_nom en  " . __FILE__ . ': line ' . __LINE__);
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

            $cAsistentes = $AsistenteRepository->getAsistentes(['id_activ' => $Qid_activ, 'id_nom' => $Qid_nom]);
            $oAsistente = $cAsistentes[0];
            $propio = $oAsistente->isPropio();
            $falta = $oAsistente->isFalta();
            $est_ok = $oAsistente->isEst_ok();
            $observ = $oAsistente->getObserv();
            $observ_est = $oAsistente->getObserv_est();
            $plaza = $oAsistente->getPlaza();
            $propietario = $oAsistente->getPropietario();

            if (ConfigGlobal::is_app_installed('actividadplazas') && !empty($propietario)) {
                $padre = strtok($propietario, '>');
                $child = strtok('>');
                if ($obj_pau !== 'PersonaEx' && $child !== ConfigGlobal::mi_delef()) {
                    exit(sprintf(_('los datos de asistencia los modifica el propietario de la plaza: %s'), $child));
                }
            }
        } else {
            $mod = 'nuevo';
            $obj_pau = !empty($Qobj_pau) ? urldecode($Qobj_pau) : '';
            $Qna = (string)($input['na'] ?? '');
            $na_val = 'p' . $Qna;
            $oDesplegablePersonas = new Desplegable();
            switch ($obj_pau) {
                case 'PersonaN':
                    $oOpciones = $GLOBALS['container']->get(PersonaNRepositoryInterface::class)->getArrayPersonas();
                    $oDesplegablePersonas->setOpciones($oOpciones);
                    $oDesplegablePersonas->setNombre('id_nom');
                    break;
                case 'PersonaNax':
                    $oOpciones = $GLOBALS['container']->get(PersonaNaxRepositoryInterface::class)->getArrayPersonas();
                    $oDesplegablePersonas->setOpciones($oOpciones);
                    $oDesplegablePersonas->setNombre('id_nom');
                    break;
                case 'PersonaAgd':
                    $oOpciones = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class)->getArrayPersonas();
                    $oDesplegablePersonas->setOpciones($oOpciones);
                    $oDesplegablePersonas->setNombre('id_nom');
                    break;
                case 'PersonaS':
                    $oOpciones = $GLOBALS['container']->get(PersonaSRepositoryInterface::class)->getArrayPersonas();
                    $oDesplegablePersonas->setOpciones($oOpciones);
                    $oDesplegablePersonas->setNombre('id_nom');
                    break;
                case 'PersonaSSSC':
                case 'PersonaEx':
                    $oOpciones = $GLOBALS['container']->get(PersonaExRepositoryInterface::class)->getArrayPersonas($na_val);
                    $oDesplegablePersonas->setOpciones($oOpciones);
                    $oDesplegablePersonas->setNombre('id_nom');
                    $obj_pau = 'PersonaEx';
                    break;
            }
            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                $oDesplegablePersonas->setAction('fnjs_cmb_propietario()');
            }
            $desplegable_personas_html = $oDesplegablePersonas->desplegable();
        }

        $propio_chk = (!empty($propio) && is_true($propio)) ? 'checked' : '';
        $falta_chk = (!empty($falta) && is_true($falta)) ? 'checked' : '';
        $est_chk = (!empty($est_ok) && is_true($est_ok)) ? 'checked' : '';

        $plazas_installed = ConfigGlobal::is_app_installed('actividadplazas');
        $desplegable_plaza_html = '';
        $desplegable_propietarios_html = '';
        $h1 = '';
        $url_ajax = '';
        if ($plazas_installed) {
            $aOpciones = PlazaId::getArrayPosiblesPlazas();
            $oDesplegablePlaza = new Desplegable();
            $oDesplegablePlaza->setNombre('plaza');
            $oDesplegablePlaza->setOpciones($aOpciones);
            $oDesplegablePlaza->setOpcion_sel((string)$plaza);
            $desplegable_plaza_html = $oDesplegablePlaza->desplegable();

            $dl_de_paso = false;
            if ($obj_pau === 'PersonaEx' && !empty($Qid_nom) && $oPersona !== null) {
                $dl_de_paso = $oPersona->getDl();
            }
            $gesActividadPlazas = $GLOBALS['container']->get(ResumenPlazasService::class);
            $gesActividadPlazas->setId_activ($Qid_activ);
            $oDesplPosiblesPropietarios = $gesActividadPlazas->getPosiblesPropietarios($dl_de_paso);
            $oDesplPosiblesPropietarios->setNombre('propietario');
            $oDesplPosiblesPropietarios->setOpcion_sel($propietario);
            $desplegable_propietarios_html = $oDesplPosiblesPropietarios->desplegable();

            $url_ajax = rtrim(ConfigGlobal::getWeb(), '/') . '/src/actividadplazas/posibles_propietarios_data';
            $oHash1 = new Hash();
            $oHash1->setUrl($url_ajax);
            $oHash1->setCamposForm('id_activ!id_nom');
            $h1 = $oHash1->linkSinValParams();
        }

        $oHash = new Hash();
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
        if (!empty($id_nom_real)) {
            $a_camposHidden['id_nom'] = (int)$id_nom_real;
        } else {
            $camposForm .= '!id_nom';
        }
        $oHash->setCamposForm($camposForm);
        $oHash->setArraycamposHidden($a_camposHidden);
        $oHash->setCamposNo('actualizar!id_nom!propio!falta!est_ok');

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $url_guardar = $web . '/src/asistentes/asistente_guardar';
        $url_self = $web . '/frontend/asistentes/controller/form_asistentes_a_una_actividad.php';

        return [
            'obj' => $obj,
            'h1' => $h1,
            'url_ajax' => $url_ajax,
            'url_guardar' => $url_guardar,
            'url_self' => $url_self,
            'id_activ' => $Qid_activ,
            'id_nom_real' => $id_nom_real,
            'ape_nom' => $ape_nom,
            'desplegable_personas_html' => $desplegable_personas_html,
            'propio_chk' => $propio_chk,
            'falta_chk' => $falta_chk,
            'est_chk' => $est_chk,
            'observ' => $observ,
            'observ_est' => $observ_est,
            'plazas_installed' => $plazas_installed,
            'hash_campos_html' => $oHash->getCamposHtml(),
            'desplegable_plaza_html' => $desplegable_plaza_html,
            'desplegable_propietarios_html' => $desplegable_propietarios_html,
        ];
    }
}
