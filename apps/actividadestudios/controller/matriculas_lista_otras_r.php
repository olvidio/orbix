<?php

use actividades\model\entity\Actividad;
use asignaturas\model\entity\GestorAsignatura;
use core\ConfigGlobal;
use notas\model\entity\GestorPersonaNotaOtraRegionStgrDB;
use personas\model\entity\GestorPersonaStgr;
use personas\model\entity\Persona;
use ubis\model\entity\GestorDelegacion;
use web\Hash;
use web\Lista;
use web\Posicion;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}
$oPosicion->recordar();

$aviso = '';
$form = '';
$traslados = '';
$Qmod = (string)filter_input(INPUT_POST, 'mod');

$Qapellido1 = (string)filter_input(INPUT_POST, 'apellido1');

$a_botones = array(
    array('txt' => _("imprimir certificado"), 'click' => "fnjs_imp_certificado(this.form)"),
);

$a_cabeceras = array(
    _("alumno"),
    _("dl"),
    _("asignaturas"),
);

$titulo_busqueda_por_apellidos = _("búsqueda por apellidos");
$titulo = '';

////// Apellidos
if (!empty($Qapellido1)) {

    $GesPersonas = new GestorPersonaStgr();
    $cPersonas = $GesPersonas->getPerosnasOtrosStgr($Qapellido1);
    $i = 0;
    $a_Nombre = [];
    $a_valores = [];
    foreach ($cPersonas as $oPersona) {
        $id_nom = $oPersona->getId_nom();
        $dl = $oPersona->getDl();
        $apellidos_nombre = $oPersona->getPrefApellidosNombre();

        $i++;
        $a_valores[$i]['sel'] = "$id_nom";
        $a_valores[$i][1] = $apellidos_nombre;
        $a_valores[$i][2] = $dl;
        $a_valores[$i][3] = "";

        $a_Nombre[$i] = $apellidos_nombre;
    }
} else {
// Buscar dl y r dependientes de la actual región del stgr:
// Se supone que si accedo a esta página es porque soy una región del stgr.
    $esquema_region_stgr = $_SESSION['session_auth']['esquema'];

    $aWhere = ['json_certificados' => 'x', '_ordre' => 'id_nom'];
    $aOperador = ['json_certificados' => 'IS NULL'];
    $gesPersonaNotaOtraRegionDB = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
    $a_notas_otras_regiones_stgr_sin_cert = $gesPersonaNotaOtraRegionDB->getPersonaNotas($aWhere, $aOperador);

    // lista asignaturas
    $gesAsignaturas = new GestorAsignatura();
    $a_asignaturas = $gesAsignaturas->getArrayAsignaturas();

    $titulo = _("Lista de alumnos de otras regiones pendientes de generar certificado");
    $i = 0;
    $a_valores = array();
    $msg_err = '';
    $str_asignaturas = '';
    $id_nom_anterior = '';
    foreach ($a_notas_otras_regiones_stgr_sin_cert as $oPersonaNotaOtraRegionDB) {
        $i++;
        $id_nom = $oPersonaNotaOtraRegionDB->getId_nom();

        if (!empty($id_nom_anterior) && $id_nom != $id_nom_anterior) {
            $oPersona = Persona::newPersona($id_nom_anterior);
            if (!is_object($oPersona)) {
                $msg_err .= "<br>$oPersona con id_nom: $id_nom_anterior en  " . __FILE__ . ": line " . __LINE__;
                $id_nom_anterior = $id_nom;
                continue;
            }

            $apellidos_nombre = $oPersona->getPrefApellidosNombre();
            $ctr = $oPersona->getCentro_o_dl();
            $dl = $oPersona->getDl();

            $a_valores[$i]['sel'] = "$id_nom_anterior";
            $a_valores[$i][1] = $apellidos_nombre;
            $a_valores[$i][2] = $dl;
            $a_valores[$i][3] = $str_asignaturas;

            // para ordenar.
            $a_Nombre[$i] = $apellidos_nombre;
            $str_asignaturas = '';
        }
        $id_asignatura = $oPersonaNotaOtraRegionDB->getId_asignatura();
        $id_activ = $oPersonaNotaOtraRegionDB->getId_activ();

        $nom_asignatura = $a_asignaturas[$id_asignatura];
        $oActividad = new Actividad($id_activ);
        $nom_activ = $oActividad->getNom_activ();
        $dl_org = $oActividad->getDl_org();

        $str_asignaturas .= empty($str_asignaturas) ? '' : ', ';
        $str_asignaturas .= trim($nom_asignatura);
        $str_asignaturas .= empty($nom_activ) ? '' : "($nom_activ)";

        $id_nom_anterior = $id_nom;
    }
    // para escribir el último. Ojo, si no hay ninguno, $id_nom = ''
    if (!empty($id_nom)) {
        $oPersona = Persona::newPersona($id_nom);
        if (!is_object($oPersona)) {
            $msg_err .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
        } else {
            $apellidos_nombre = $oPersona->getPrefApellidosNombre();
            $ctr = $oPersona->getCentro_o_dl();
            $dl = $oPersona->getDl();

            $a_valores[$i + 1]['sel'] = "$id_nom";
            $a_valores[$i + 1][1] = $apellidos_nombre;
            $a_valores[$i + 1][2] = $dl;
            $a_valores[$i + 1][3] = $str_asignaturas;

            // para ordenar.
            $a_Nombre[$i + 1] = $apellidos_nombre;
            $str_asignaturas = '';
        }
    }

}


// ordenar por alumno
if (!empty($a_valores)) {
    array_multisort(
        $a_Nombre, SORT_STRING,
        $a_valores);
}
//OJO!! hay añadirlos después de ordenar.
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oHash = new Hash();
$oHash->setCamposNo('sel!mod!pau!scroll_id');
$a_camposHidden = array(
    'id_dossier' => 3005,
    'permiso' => 3,
    'obj_pau' => 'Actividad',
    'queSel' => 'asig',
);
$oHash->setArraycamposHidden($a_camposHidden);


if (!empty($msg_err)) {
    echo $msg_err;
}

$oTabla = new Lista();
$oTabla->setId_tabla('mtr_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);


$oHashApellidos = new web\Hash();
$oHashApellidos->setCamposForm('apellido1');
$a_camposHiddenP = array();
$oHashApellidos->setArraycamposHidden($a_camposHiddenP);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'mod' => $Qmod,
    'oTabla' => $oTabla,
    'titulo' => $titulo,
    'titulo_busqueda_por_apellidos' => $titulo_busqueda_por_apellidos,
    'aviso' => $aviso,
    'oHashApellidos' => $oHashApellidos,
    'Qapellido1' => $Qapellido1,
];

$oView = new core\View('actividadestudios/controller');
$oView->renderizar('matriculas_otras_r.phtml', $a_campos);
