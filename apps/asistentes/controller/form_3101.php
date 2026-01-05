<?php
/**
 * Muestra un formulario para introducir/cambiar los datos de la asistencia
 * de una persona a una actividad
 *
 *
 * @param string $_POST ['pau']  para el controlador dossiers_ver
 * @param integer $_POST ['id_pau']  para el controlador dossiers_ver
 * @param string $_POST ['obj_pau']  para el controlador dossiers_ver
 * @param integer $_POST ['id_dossier']  para el controlador dossiers_ver
 * @param string $_POST ['mod']  para el controlador dossiers_ver
 * En el caso de modificar:
 * @param string $_POST ['mod_curso']  para mantener la selección del curso
 * @param integer $_POST ['permiso'] valores 1, 2, 3
 * @param integer $_POST ['scroll_id']
 * @param array $_POST ['sel'] con id_activ#id_asignatura
 * En el caso de nuevo:
 * @param string $_POST ['que_dl'] la propia dl o vacio para otras
 * @param integer $_POST ['id_tipo'] selección del tipo de actividad
 *
 * @package    orbix
 * @subpackage    asistentes
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 *
 */

use core\ConfigGlobal;
use core\ViewPhtml;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\domain\ResumenPlazas;
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

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qactualizar = (integer)filter_input(INPUT_POST, 'actualizar');
if (empty($Qactualizar)) {
    $oPosicion->recordar();
}

$Qpermiso = (string)filter_input(INPUT_POST, 'permiso');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_nom = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
}

$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

if (empty($Qid_activ)) {
    $Qid_activ = $Qid_pau;
}


$AsistenteRepository = $GLOBALS['container']->get(AsistenteRepositoryInterface::class);

$obj = 'asistentes\\model\\entity\\Asistente';

/* Mirar si la actividad es mia o no */
$ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
$oActividad = $ActividadAllRepository->findById($Qid_activ);

if (!empty($Qid_nom)) { //caso de modificar
    $mod = "editar";
    $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
    if (!is_object($oPersona)) {
        $msg_err = "<br>No encuentro a nadie con id_nom: $Qid_nom en  " . __FILE__ . ": line " . __LINE__;
        exit($msg_err);
    }
    // Hay que especificar el tipo de personas para poder crear un nuevo asistente desde la ficha
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

    $aWhere = array('id_activ' => $Qid_activ, 'id_nom' => $Qid_nom);
    $cAsistentes = $AsistenteRepository->getAsistentes($aWhere);
    $oAsistente = $cAsistentes[0];

    $propio = $oAsistente->isPropio();
    $falta = $oAsistente->isFalta();
    $est_ok = $oAsistente->isEst_ok();
    $observ = $oAsistente->getObserv();
    $observ_est = $oAsistente->getObserv_est();
    $plaza = $oAsistente->getPlaza();
    $propietario = $oAsistente->getPropietario();

    if (ConfigGlobal::is_app_installed('actividadplazas')) {
        if (!empty($propietario)) {
            $padre = strtok($propietario, '>');
            $child = strtok('>');
            if ($obj_pau !== 'PersonaEx' && $child !== ConfigGlobal::mi_delef()) {
                exit (sprintf(_("los datos de asistencia los modifica el propietario de la plaza: %s"), $child));
            }
        }
    }
    $oDesplegablePersonas = [];
} else { //caso de nuevo asistente
    $mod = "nuevo";
    $id_nom_real = '';
    $ape_nom = '';
    $propio = "t"; //valor por defecto
    $observ = ""; //valor por defecto
    $observ_est = ""; //valor por defecto
    $plaza = PlazaId::PEDIDA; //valor por defecto
    $propietario = ''; //valor por defecto
    $Qobj_pau = !empty($Qobj_pau) ? urldecode($Qobj_pau) : '';
    $obj_pau = $Qobj_pau;
    $Qna = (string)filter_input(INPUT_POST, 'na');
    $na_val = 'p' . $Qna;
    $oDesplegablePersonas = new Desplegable();
    switch ($obj_pau) {
        case 'PersonaN':
            $PersonaNRepository = $GLOBALS['container']->get(PersonaNRepositoryInterface::class);
            $oOpciones = $PersonaNRepository->getArrayPersonas();
            $oDesplegablePersonas->setOpciones($oOpciones);
            $oDesplegablePersonas->setNombre('id_nom');
            break;
        case 'PersonaNax':
            $PersonaNaxRepository = $GLOBALS['container']->get(PersonaNaxRepositoryInterface::class);
            $oOpciones = $PersonaNaxRepository->getArrayPersonas();
            $oDesplegablePersonas->setOpciones($oOpciones);
            $oDesplegablePersonas->setNombre('id_nom');
            break;
        case 'PersonaAgd':
            $PersonaAgdRepository = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class);
            $oOpciones = $PersonaAgdRepository->getArrayPersonas();
            $oDesplegablePersonas->setOpciones($oOpciones);
            $oDesplegablePersonas->setNombre('id_nom');
            break;
        case 'PersonaS':
            $PersonaSRepository = $GLOBALS['container']->get(PersonaSRepositoryInterface::class);
            $oOpciones = $PersonaSRepository->getArrayPersonas();
            $oDesplegablePersonas->setOpciones($oOpciones);
            $oDesplegablePersonas->setNombre('id_nom');
            break;
        case 'PersonaSSSC':
        case 'PersonaEx':
            $PersonaExRepository = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
            $oOpciones = $PersonaExRepository->getArrayPersonas($na_val);
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

if (ConfigGlobal::is_app_installed('actividadplazas')) {
    $aOpciones = PlazaId::getArrayPosiblesPlazas();
    $oDesplegablePlaza = new Desplegable();
    $oDesplegablePlaza->setNombre('plaza');
    $oDesplegablePlaza->setOpciones($aOpciones);
    $oDesplegablePlaza->setOpcion_sel($plaza);

    $dl_de_paso = FALSE;
    if ($obj_pau === 'PersonaEx') {
        if (!empty($Qid_nom)) { //caso de modificar
            $dl_de_paso = $oPersona->getDl();
        }
    }
    $gesActividadPlazas = new ResumenPlazas();
    $gesActividadPlazas->setId_activ($Qid_activ);
    $oDesplPosiblesPropietarios = $gesActividadPlazas->getPosiblesPropietarios($dl_de_paso);
    $oDesplPosiblesPropietarios->setNombre('propietario');
    $oDesplPosiblesPropietarios->setOpcion_sel($propietario);

    $url_ajax = ConfigGlobal::getWeb() . '/apps/actividadplazas/controller/gestion_plazas_ajax.php';
    $oHash1 = new Hash();
    $oHash1->setUrl($url_ajax);
    $oHash1->setCamposForm('que!id_activ!id_nom');
    $h1 = $oHash1->linkSinVal();
} else {
    $h1 = '';
    $url_ajax = '';
    $oDesplegablePlaza = '';
    $oDesplPosiblesPropietarios = '';
}

$oHash = new Hash();
$camposForm = 'observ!observ_est';
if (ConfigGlobal::is_app_installed('actividadplazas')) {
    $camposForm .= '!plaza!propietario';
}
$a_camposHidden = array(
    'id_activ' => $Qid_activ,
    'obj_pau' => $obj_pau,
    'mod' => $mod,
    'actualizar' => 0,
);
if (!empty($id_nom_real)) {
    $a_camposHidden['id_nom'] = $id_nom_real;
} else {
    $camposForm .= '!id_nom';
}
$oHash->setCamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);
// EN el caso de guradar y añadir uno nuevo, se pone id_nom=0.
$oHash->setCamposNo('actualizar!id_nom!propio!falta!est_ok');


//$oPosicion->addParametro('mod',$mod,0);

$a_campos = ['obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'h1' => $h1,
    'url_ajax' => $url_ajax,
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

$oView = new ViewPhtml('asistentes\controller');
$oView->renderizar('form_3101.phtml', $a_campos);
