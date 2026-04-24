<?php

/**
 * Form de alta / edicion de un `Asistente` desde el dossier
 * `asistentes_a_una_actividad` (3101).
 *
 * Sucesor de `apps/asistentes/controller/form_3101.php`. URL canonica.
 *
 * @param integer $_POST['id_pau']      id de la actividad
 * @param string  $_POST['obj_pau']     clase de persona destino (PersonaN, PersonaS, ...)
 * @param integer $_POST['id_dossier']  id_tipo_dossier de la vista padre
 * @param string  $_POST['mod']         nuevo|editar
 * En caso de modificar:
 * @param array   $_POST['sel']         seleccion grid: id_nom
 * En caso de nuevo:
 * @param integer $_POST['id_activ']    id actividad (fallback id_pau)
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
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
use web\Desplegable;
use web\Hash;
use function core\is_true;

require_once 'frontend/shared/global_header_front.inc';

$Qactualizar = (int) filter_input(INPUT_POST, 'actualizar');
if (empty($Qactualizar)) {
    $oPosicion->recordar();
}

$a_sel = (array) filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $Qid_nom = (int) strtok($a_sel[0], '#');
} else {
    $Qid_nom = (int) filter_input(INPUT_POST, 'id_nom');
}

$Qid_activ = (int) filter_input(INPUT_POST, 'id_activ');
$Qid_pau = (int) filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string) filter_input(INPUT_POST, 'obj_pau');
if (empty($Qid_activ)) {
    $Qid_activ = $Qid_pau;
}

$AsistenteRepository = $GLOBALS['container']->get(AsistenteRepositoryInterface::class);
$obj = 'asistentes\\model\\entity\\Asistente';

$ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
$oActividad = $ActividadAllRepository->findById($Qid_activ);

$oDesplegablePersonas = [];
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
    $id_nom_real = $Qid_nom;

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
            exit(sprintf(_("los datos de asistencia los modifica el propietario de la plaza: %s"), $child));
        }
    }
} else {
    $mod = 'nuevo';
    $obj_pau = !empty($Qobj_pau) ? urldecode($Qobj_pau) : '';
    $Qna = (string) filter_input(INPUT_POST, 'na');
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
}
$propio_chk = (!empty($propio) && is_true($propio)) ? 'checked' : '';
$falta_chk = (!empty($falta) && is_true($falta)) ? 'checked' : '';
$est_chk = (!empty($est_ok) && is_true($est_ok)) ? 'checked' : '';

$oDesplegablePlaza = '';
$oDesplPosiblesPropietarios = '';
$h1 = '';
$url_ajax = '';
if (ConfigGlobal::is_app_installed('actividadplazas')) {
    $aOpciones = PlazaId::getArrayPosiblesPlazas();
    $oDesplegablePlaza = new Desplegable();
    $oDesplegablePlaza->setNombre('plaza');
    $oDesplegablePlaza->setOpciones($aOpciones);
    $oDesplegablePlaza->setOpcion_sel($plaza);

    $dl_de_paso = false;
    if ($obj_pau === 'PersonaEx' && !empty($Qid_nom) && $oPersona !== null) {
        $dl_de_paso = $oPersona->getDl();
    }
    $gesActividadPlazas = $GLOBALS['container']->get(ResumenPlazasService::class);
    $gesActividadPlazas->setId_activ($Qid_activ);
    $oDesplPosiblesPropietarios = $gesActividadPlazas->getPosiblesPropietarios($dl_de_paso);
    $oDesplPosiblesPropietarios->setNombre('propietario');
    $oDesplPosiblesPropietarios->setOpcion_sel($propietario);

    $url_ajax = rtrim(ConfigGlobal::getWeb(), '/') . '/src/actividadplazas/posibles_propietarios_data';
    $oHash1 = new Hash();
    $oHash1->setUrl($url_ajax);
    $oHash1->setCamposForm('id_activ!id_nom');
    $h1 = $oHash1->linkSinValParams();
}

$oHash = new Hash();
$camposForm = 'observ!observ_est';
if (ConfigGlobal::is_app_installed('actividadplazas')) {
    $camposForm .= '!plaza!propietario';
}
$a_camposHidden = [
    'id_activ' => $Qid_activ,
    'obj_pau' => $obj_pau,
    'mod' => $mod,
    'actualizar' => 0,
];
if (!empty($id_nom_real)) {
    $a_camposHidden['id_nom'] = $id_nom_real;
} else {
    $camposForm .= '!id_nom';
}
$oHash->setCamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposNo('actualizar!id_nom!propio!falta!est_ok');

$web = rtrim(ConfigGlobal::getWeb(), '/');
$url_guardar = $web . '/src/asistentes/asistente_guardar';
$url_self = $web . '/frontend/asistentes/controller/form_asistentes_a_una_actividad.php';

$a_campos = [
    'obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'h1' => $h1,
    'url_ajax' => $url_ajax,
    'url_guardar' => $url_guardar,
    'url_self' => $url_self,
    'id_activ' => $Qid_activ,
    'id_nom_real' => $id_nom_real,
    'ape_nom' => $ape_nom,
    'oDesplegablePersonas' => $oDesplegablePersonas,
    'propio_chk' => $propio_chk,
    'falta_chk' => $falta_chk,
    'est_chk' => $est_chk,
    'observ' => $observ,
    'observ_est' => $observ_est,
    'oDesplegablePlaza' => $oDesplegablePlaza,
    'oDesplPosiblesPropietarios' => $oDesplPosiblesPropietarios,
];

(new ViewNewPhtml('frontend\\asistentes\\controller'))
    ->renderizar('form_asistentes_a_una_actividad.phtml', $a_campos);
