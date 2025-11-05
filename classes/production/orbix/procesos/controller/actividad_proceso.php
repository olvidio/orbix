<?php

use actividades\model\entity\ActividadAll;
use core\ConfigGlobal;
use core\ViewTwig;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_activ = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
}

$aQuery = array('pau' => 'a',
    'id_pau' => $Qid_activ,
    'obj_pau' => 'Actividad');
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$godossiers = Hash::link('apps/dossiers/controller/dossiers_ver.php?' . http_build_query($aQuery));

$alt = _("ver dossiers");
$dos = _("dossiers");

$oActividad = new ActividadAll($Qid_activ);
$nom_activ = $oActividad->getNom_activ();

$permiso_calendario = FALSE;
if (($_SESSION['oPerm']->have_perm_oficina('calendario')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
    $permiso_calendario = TRUE;
}

$oHashGenerar = new Hash();
$a_camposHiddenG = array(
    'que' => 'generar',
    'id_activ' => $Qid_activ,
);
$oHashGenerar->setArraycamposHidden($a_camposHiddenG);
$param_generar = $oHashGenerar->getParamAjax();

$oHashActualizar = new Hash();
$a_camposHiddenA = array(
    'que' => 'get',
    'id_activ' => $Qid_activ,
);
$oHashActualizar->setArraycamposHidden($a_camposHiddenA);
$param_actualizar = $oHashActualizar->getParamAjax();

$oHash1 = new Hash();
$oHash1->setUrl(ConfigGlobal::getWeb() . '/apps/procesos/controller/actividad_proceso_ajax.php');
$oHash1->setCamposForm('force!que!id_item!completado!observ');
$h_update = $oHash1->linkSinVal();

$url_ajax = 'apps/procesos/controller/actividad_proceso_ajax.php';
$txt_confirm = _("¿Está seguro que desea crear el proceso de nuevo?");

$a_campos = ['oPosicion' => $oPosicion,
    'godossiers' => $godossiers,
    'alt' => $alt,
    'dos' => $dos,
    'nom_activ' => $nom_activ,
    'permiso_calendario' => $permiso_calendario,
    'web_icons' => ConfigGlobal::getWeb_icons(),
    'url_ajax' => $url_ajax,
    'id_activ' => $Qid_activ,
    'param_generar' => $param_generar,
    'param_actualizar' => $param_actualizar,
    'h_update' => $h_update,
    'txt_confirm' => $txt_confirm,
];

$oView = new ViewTwig('procesos/controller');
$oView->renderizar('actividad_proceso.html.twig', $a_campos);