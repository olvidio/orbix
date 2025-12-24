<?php
/**
 * Muestra un formulario para introducir/cambiar las notas de una persona
 *
 *
 * @param string $_POST ['pau']  para el controlador dossiers_ver
 * @param integer $_POST ['id_pau']  para el controlador dossiers_ver
 * @param string $_POST ['obj_pau']  para el controlador dossiers_ver
 * @param integer $_POST ['id_dossier']  para el controlador dossiers_ver
 * @param string $_POST ['mod']  para el controlador dossiers_ver
 * En el caso de modificar:
 * @param integer $_POST ['permiso'] valores 1, 2, 3
 * @param integer $_POST ['scroll_id']
 * @param array $_POST ['sel'] con id_activ#id_asignatura
 *
 * @package    orbix
 * @subpackage    notas
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 *
 */

use core\ConfigGlobal;
use core\ViewPhtml;
use notas\model\entity\GestorPersonaNotaDB;
use notas\model\entity\PersonaNotaDB;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\entity\Nota;
use src\personas\domain\entity\Persona;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$obj = 'notas\\model\\entity\\PersonaNotaDB';

$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qpermiso = (integer)filter_input(INPUT_POST, 'permiso');
$Qmod = (string)filter_input(INPUT_POST, 'mod');

$sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($sel)) { //vengo de un checkbox
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
    if ($Qpau === "p") {
        $id_nivel_real = strtok($sel[0], "#");
        $Qid_asignatura_real = strtok("#");
    }
} else {
    if (!empty($Qmod) && $Qmod !== 'nuevo') {
        $Qid_asignatura_real = (string)filter_input(INPUT_POST, 'id_asignatura_real');
    } else {
        $Qid_asignatura_real = '';
    }
}

$NotaRepository = $GLOBALS['container']->get(NotaRepositoryInterface::class);
$aOpciones = $NotaRepository->getArrayNotas();
$oDesplNotas = new Desplegable();
$oDesplNotas->setOpciones($aOpciones);
$oDesplNotas->setNombre('id_situacion');

$cNotas = $NotaRepository->getArrayNotasNoSuperadas();
$lista_situacion_no_acta = '"11"'; // Para el caso de 'exento', es superada pero sin acta.
foreach ($cNotas as $id_situacion) {
    $lista_situacion_no_acta .= ',"' . $id_situacion . '"';
}

$AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);

if (!empty($Qid_asignatura_real)) { //caso de modificar
    $mod = "editar";
    $id_asignatura = $Qid_asignatura_real;
    // Ahora pueden existir 2.
    // - Si es nota_real (tipo_acta = 1) puede estar en la tabla e_notas_otra_region...
    // - Si es nota_certificado (tipo_acta = 2) en la tabla e_notas_dl
    // Debería mostrar la de e_notas_dl. Ordeno por tipo_acta
    $aWhere = [];
    $aWhere['_ordre'] = 'tipo_acta DESC';
    $aWhere['id_nom'] = $Qid_pau;
    $aWhere['id_asignatura'] = $Qid_asignatura_real;
    $GesPersonaNotas = new GestorPersonaNotaDB();
    $cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhere);
    if ($cPersonaNotas === false) {
        exit("Error en la consulta a la base de datos en: " . __FILE__ . ": line " . __LINE__);
    }
    $oPersonaNota = $cPersonaNotas[0]; // solo debería existir una.
    $id_situacion = $oPersonaNota->getId_situacion();
    $nota_num = $oPersonaNota->getNota_num();
    $nota_max = $oPersonaNota->getNota_max();
    $acta = $oPersonaNota->getActa();
    $tipo_acta = $oPersonaNota->getTipo_acta();
    $oF_acta = $oPersonaNota->getF_acta();
    $f_acta = $oF_acta->getFromLocal();
    $f_acta_iso = $oF_acta->format('Y-m-d');
    $preceptor = $oPersonaNota->getPreceptor();
    $id_preceptor = $oPersonaNota->getId_preceptor();
    $detalle = $oPersonaNota->getDetalle();
    $epoca = $oPersonaNota->getEpoca();
    $id_activ = $oPersonaNota->getId_activ();

    $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
    $oAsignatura = $AsignaturaRepository->findById($Qid_asignatura_real);
    if ($oAsignatura === null) {
        throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $Qid_asignatura_real));
    }
    $nombre_corto = $oAsignatura->getNombre_corto();
    if ($oPersonaNota->getId_asignatura() > 3000) {
        $id_nivel = $oPersonaNota->getId_nivel();
    } else {
        $id_nivel = $oAsignatura->getId_nivel();
    }

    $ProfesorRepository = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
    $cProfesores = $ProfesorRepository->getProfesoresStgr();
    $aProfesores = [];
    $msg_err = '';
    foreach ($cProfesores as $oProfesor) {
        $id_nom = $oProfesor->getId_nom();
        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if ($oPersona === null) {
            $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
            continue;
        }
        $ap_nom = $oPersona->getPrefApellidosNombre();
        $aProfesores[$id_nom] = $ap_nom;
    }
    uasort($aProfesores, 'core\strsinacentocmp');

    $oDesplProfesores = new Desplegable();
    $oDesplProfesores->setNombre('id_preceptor');
    $oDesplProfesores->setOpciones($aProfesores);
    $oDesplProfesores->setOpcion_sel($id_preceptor);
    $oDesplProfesores->setBlanco(1);

    $cOpcionales = [];
    $aFaltan = [];
    $oDesplNiveles = [];
} else { //caso de nueva asignatura
    $mod = "nuevo";
    $id_situacion = '';
    $nota_num = '';
    $nota_max = '';
    $acta = '';
    $tipo_acta = '';
    $f_acta = '';
    $f_acta_iso = '';
    $preceptor = '';
    $id_preceptor = '';
    $detalle = '';
    $epoca = '';
    $id_activ = '';
    $oDesplProfesores = [];
    // todas las asignaturas
    $aWhere = [];
    $aOperador = [];
    $aWhere['status'] = 't';
    $aWhere['id_nivel'] = 3000;
    $aOperador['id_nivel'] = '<';
    $aWhere['_ordre'] = 'id_nivel';
    $cAsignaturas = $AsignaturaRepository->getAsignaturas($aWhere, $aOperador);
    // todas las opcionales
    $aWhere = [];
    $aOperador = [];
    $aWhere['status'] = 't';
    $aWhere['id_nivel'] = '3000,5000';
    $aOperador['id_nivel'] = 'BETWEEN';
    $aWhere['_ordre'] = 'nombre_corto';
    $cOpcionales = $AsignaturaRepository->getAsignaturas($aWhere, $aOperador);
    // Asignaturas superadas
    $NotaRepository = $GLOBALS['container']->get(NotaRepositoryInterface::class);
    $aSuperadas = $NotaRepository->getArrayNotasSuperadas();
    $aWhere = [];
    $aOperador = [];
    $aWhere['id_situacion'] = implode(',', $aSuperadas);
    $aOperador['id_situacion'] = 'IN';
    $aWhere['id_nom'] = $Qid_pau;
    $aWhere['id_nivel'] = 3000;
    $aOperador['id_nivel'] = '<';
    $aWhere['_ordre'] = 'id_nivel';
    $GesPersonaNotas = new GestorPersonaNotaDB();
    $cAsignaturasSuperadas = $GesPersonaNotas->getPersonaNotas($aWhere, $aOperador);
    $aSuperadas = [];
    foreach ($cAsignaturasSuperadas as $oAsignatura) {
        $id_nivel = $oAsignatura->getId_nivel();
        $id_asignatura = $oAsignatura->getId_asignatura();
        $aSuperadas[$id_nivel] = $id_asignatura;
    }
    // asignaturas posibles
    $aFaltan = [];
    foreach ($cAsignaturas as $oAsignatura) {
        $id_nivel = $oAsignatura->getId_nivel();
        $id_asignatura = $oAsignatura->getId_asignatura();
        $nombre_corto = $oAsignatura->getNombre_corto();
        if (array_key_exists($id_nivel, $aSuperadas)) continue;
        $aFaltan[$id_nivel] = $nombre_corto;
    }
    // Añado Fin Bienio y Fin Cuadrienio
    $aFaltan[9997] = '---------';
    $aFaltan[9998] = _("fin cuadrienio");
    $aFaltan[9999] = _("fin bienio");

    $oDesplNiveles = new Desplegable();
    $oDesplNiveles->setNombre('id_nivel');
    $oDesplNiveles->setOpciones($aFaltan);
    $oDesplNiveles->setBlanco(1);
    $oDesplNiveles->setAction('fnjs_cmb_opcional()');
}

// Valores por defecto
$nota_max_default = $_SESSION['oConfig']->getNotaMax();
$nota_max = empty($nota_max) ? $nota_max_default : $nota_max;
$id_situacion = empty($id_situacion) ? Nota::NUMERICA : $id_situacion;

if (!empty($preceptor)) {
    $chk_preceptor = "checked";
} else {
    $chk_preceptor = "";
}
$oDesplNotas->setOpcion_sel($id_situacion);

if (!empty($tipo_acta)) {
    if ($tipo_acta == PersonaNotaDB::FORMATO_ACTA) {
        $chk_acta = "checked";
    } else {
        $chk_acta = "";
    }
    if ($tipo_acta == PersonaNotaDB::FORMATO_CERTIFICADO) {
        $chk_certificado = "checked";
    } else {
        $chk_certificado = "";
    }
} else {
    $chk_acta = "checked";
    $chk_certificado = "";
}

if (!empty($epoca)) {
    if ($epoca == PersonaNotaDB::EPOCA_CA) {
        $chk_epoca_ca = "checked";
    } else {
        $chk_epoca_ca = "";
    }
    if ($epoca == PersonaNotaDB::EPOCA_INVIERNO) {
        $chk_epoca_inv = "checked";
    } else {
        $chk_epoca_inv = "";
    }
    if ($epoca == PersonaNotaDB::EPOCA_OTRO) {
        $chk_epoca_otro = "checked";
    } else {
        $chk_epoca_otro = "";
    }
} else {
    $chk_epoca_ca = "checked";
    $chk_epoca_inv = "";
    $chk_epoca_otro = "";
}

if (!empty($id_activ)) {
    $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
    $oActividad = $ActividadAllRepository->findById($id_activ);
    $nom_activ = $oActividad->getNom_activ();
} else {
    $nom_activ = '';
}


// miro cuales son las opcionales genéricas, para la funcion
//  fnjs_cmb_opcional de javascript.
// la condicion es que tengan id_sector=1
$aWhere = [];
$aOperador = [];
$aWhere['status'] = 't';
$aWhere['id_sector'] = 1;
$aWhere['id_nivel'] = 3000;
$aOperador['id_nivel'] = '<';
$aWhere['_ordre'] = 'nombre_corto';
$cOpcionalesGenericas = $AsignaturaRepository->getAsignaturas($aWhere, $aOperador);
$condicion = '';
$lista_nivel_op = '';
foreach ($cOpcionalesGenericas as $oOpcional) {
    $id_nivel_j = $oOpcional->getId_nivel();
    $condicion .= "id==" . $id_nivel_j . " || ";
    $lista_nivel_op .= $id_nivel_j . ",";
}
$condicion_js = substr($condicion, 0, -4);


$oHash = new Hash();
$campos_chk = '!preceptor';
$camposForm = 'preceptor!nota_num!nota_max!id_situacion!acta!tipo_acta!f_acta!preceptor!id_preceptor!epoca!id_activ!detalle';
$camposNo = 'refresh!id_preceptor!id_activ' . $campos_chk;
$a_camposHidden = array(
    'campos_chk' => $campos_chk,
    'mod' => $mod,
    'pau' => $Qpau,
    'id_pau' => $Qid_pau,
    'obj_pau' => $Qobj_pau,
    'permiso' => $Qpermiso,
    'id_activ' => $id_activ,
);

if (!empty($Qid_asignatura_real)) { //caso de modificar
    $a_camposHidden['id_asignatura_real'] = $Qid_asignatura_real;
    $a_camposHidden['id_asignatura'] = $Qid_asignatura_real;
    $a_camposHidden['id_nivel'] = $id_nivel;
} else {
    $camposForm .= '!id_nivel!id_asignatura';
    $camposNo .= '!id_nivel!id_asignatura';
}
$oHash->setCamposForm($camposForm);
$oHash->setcamposNo($camposNo);
$oHash->setArraycamposHidden($a_camposHidden);

$url_ajax = ConfigGlobal::getWeb() . '/apps/notas/controller/notas_ajax.php';
$oHash1 = new Hash();
$oHash1->setUrl($url_ajax);
$oHash1->setCamposForm('que!id_nom');
//$oHash1->setCamposNo('id_nom'); 
$h1 = $oHash1->linkSinVal();
$oHash2 = new Hash();
$oHash2->setUrl($url_ajax);
$oHash2->setCamposForm('que');
$h2 = $oHash2->linkSinVal();


$oHashMod = new Hash();
$oHashMod->setUrl($url_ajax);
$oHashMod->setCamposForm('dl_org!f_acta_iso!que');
$h_modificar = $oHashMod->linkSinVal();

$oHashActa = new Hash();
$oHashActa->setUrl($url_ajax);
$oHashActa->setCamposForm('acta!que');
$h_acta = $oHashActa->linkSinVal();

$op_genericas = $AsignaturaRepository->getListaOpGenericas('json');

$a_campos = [
    'obj' => $obj, //sirve para comprobar campos
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'h1' => $h1,
    'h2' => $h2,
    'h_modificar' => $h_modificar,
    'h_acta' => $h_acta,
    'op_genericas' => $op_genericas,
    'condicion_js' => $condicion_js,
    'Qid_asignatura_real' => $Qid_asignatura_real,
    'nombre_corto' => $nombre_corto,
    'oDesplNiveles' => $oDesplNiveles,
    'nota_num' => $nota_num,
    'nota_max' => $nota_max,
    'nota_max_default' => $nota_max_default,
    'oDesplNotas' => $oDesplNotas,
    'chk_acta' => $chk_acta,
    'chk_certificado' => $chk_certificado,
    'acta' => $acta,
    'f_acta' => $f_acta,
    'f_acta_iso' => $f_acta_iso,
    'chk_preceptor' => $chk_preceptor,
    'id_preceptor' => $id_preceptor,
    'oDesplProfesores' => $oDesplProfesores,
    'epoca' => $epoca,
    'chk_epoca_ca' => $chk_epoca_ca,
    'chk_epoca_inv' => $chk_epoca_inv,
    'chk_epoca_otro' => $chk_epoca_otro,
    'nom_activ' => $nom_activ,
    'detalle' => $detalle,
    'lista_situacion_no_acta' => $lista_situacion_no_acta,
    'locale_us' => ConfigGlobal::is_locale_us(),
];

$oView = new ViewPhtml('notas\model');
$oView->renderizar('form_1011.phtml', $a_campos);