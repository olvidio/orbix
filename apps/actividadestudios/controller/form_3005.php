<?php

use actividades\model\entity as actividades;
use asignaturas\model\entity as asignaturas;
use actividadestudios\model\entity as actividadestudios;
use profesores\model\entity as profesores;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$obj = 'actividadestudios\\model\\entity\\ActividadAsignatura';

$oPosicion->recordar();

$Qpau = (string)filter_input(INPUT_POST, 'pau');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_activ = (integer)strtok($a_sel[0], "#");
    $Qid_asignatura = (integer)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    if ($Qpau == 'a') {
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_pau');
    }
    $Qid_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');
//	$Qid_activ = (integer) filter_input(INPUT_POST, 'id_activ');
}

$chk_avisado = '';
$chk_confirmado = '';
$chk_preceptor = '';
$oDesplAsignaturas = array();

if (!empty($Qid_asignatura)) { //caso de modificar
    $mod = "editar";
    $GesProfesores = new profesores\GestorProfesor();
    $oDesplProfesores = $GesProfesores->getDesplProfesoresAsignatura($Qid_asignatura);
    $oDesplProfesores->setOpcion_sel(-1);

    $oActividadAsignatura = new actividadestudios\ActividadAsignaturaDl();
    $oActividadAsignatura->setId_activ($Qid_activ);
    $oActividadAsignatura->setId_asignatura($Qid_asignatura);
    $oActividadAsignatura->DBCarregar();

    $id_profesor = $oActividadAsignatura->getId_profesor();
    if (!empty($id_profesor)) {
        $oDesplProfesores->setOpcion_sel($id_profesor);
    }
    $aviso = $oActividadAsignatura->getAvis_profesor();
    $chk_avisado = ($aviso == "a") ? "selected" : '';
    $chk_confirmado = ($aviso == "c") ? "selected" : '';
    $tipo = $oActividadAsignatura->getTipo();
    $chk_preceptor = ($tipo == "p") ? "selected" : '';
    $f_ini = $oActividadAsignatura->getF_ini()->getFromLocal();
    $f_fin = $oActividadAsignatura->getF_fin()->getFromLocal();

    $oAsignatura = new asignaturas\Asignatura($Qid_asignatura);
    $nombre_corto = $oAsignatura->getNombre_corto();
    $creditos = $oAsignatura->getCreditos();

    $primary_key_s = "id_activ=$Qid_activ AND id_asignatura=$Qid_asignatura";
} else { //caso de nueva asignatura
    $mod = "nuevo";
    $nombre_corto = '';
    $GesProfesores = new profesores\GestorProfesorActividad();
    $oDesplProfesores = $GesProfesores->getListaProfesoresActividad(array($Qid_activ));
    $oDesplProfesores->setOpcion_sel(-1);

    $f_ini = '';
    $f_fin = '';
    if (!empty($Qid_activ)) {
        $GesAsignaturas = new asignaturas\GestorAsignatura();
        $oDesplAsignaturas = $GesAsignaturas->getListaAsignaturas(false);
        $oDesplAsignaturas->setNombre('id_asignatura');
        $oDesplAsignaturas->setAction("fnjs_mas_profes('asignatura')");
    } else {
        exit (_("debería haber un nombre de asignatura"));
        $id_dossier = (integer)filter_input(INPUT_POST, 'id_dossier');
        $tabla_pau = (string)filter_input(INPUT_POST, 'tabla_pau');
        $go_to = urlencode(core\ConfigGlobal::getWeb() . "/apps/dossiers/controller/dossiers_ver.php?pau=a&id_pau=$Qid_activ&id_dossier=$id_dossier&tabla_pau=$tabla_pau&permiso=3");
        $oPosicion2 = new web\Posicion();
        echo $oPosicion2->ir_a($go_to);
    }
}

$oDesplProfesores->setNombre('id_profesor');
$oDesplProfesores->setBlanco('t');

$oHash = new web\Hash();
$camposForm = 'f_ini!f_fin!tipo!id_profesor';
$oHash->setCamposNo('mod!avis_profesor');
$a_camposHidden = array(
    'id_activ' => $Qid_activ,
);
if (!empty($Qid_asignatura)) {
    $a_camposHidden['id_asignatura'] = $Qid_asignatura;
    $a_camposHidden['primary_key_s'] = $primary_key_s;
} else {
    $camposForm .= '!id_asignatura';
}
$oHash->setCamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);


$oHashTipo = new web\Hash();
$oHashTipo->setUrl('apps/actividadestudios/controller/lista_profesores_ajax.php');
$oHashTipo->setCamposForm('salida');
$h = $oHashTipo->linkSinVal();

$oHashTipo->setCamposForm('salida!id_activ');
$h1 = $oHashTipo->linkSinVal();

$oHashTipo->setCamposForm('salida!id_activ!id_asignatura');
$h2 = $oHashTipo->linkSinVal();

$a_campos = ['obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'h' => $h,
    'h1' => $h1,
    'h2' => $h2,
    'mod' => $mod,
    'id_activ' => $Qid_activ,
    'id_asignatura' => $Qid_asignatura,
    'nombre_corto' => $nombre_corto,
    'oDesplAsignaturas' => $oDesplAsignaturas,
    'oDesplProfesores' => $oDesplProfesores,
    'chk_preceptor' => $chk_preceptor,
    'chk_avisado' => $chk_avisado,
    'chk_confirmado' => $chk_confirmado,
    'f_ini' => $f_ini,
    'f_fin' => $f_fin,
];

$oView = new core\View('actividadestudios/controller');
$oView->renderizar('form_3005.phtml', $a_campos);
