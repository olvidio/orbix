<?php

use devel\model\modulosConfig;

/**
 * Funciones más comunes de la aplicación
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************
require_once("apps/web/func_web.php");

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qmod = (string)filter_input(INPUT_POST, 'mod'); // 0 -> existe, 1->nuevo


$Qrefresh = (integer)\filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

if ($Qmod == 'nuevo') {
    $Qid_mod = '';
    $nom = '';
    $descripcion = '';
    $mods_req = '';
    $apps_req = '';
} else {
    $a_sel = (array)\filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!empty($a_sel)) { //vengo de un checkbox
        $Qid_mod = (integer)strtok($a_sel[0], "#");
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel', $a_sel, 1);
        $scroll_id = (integer)\filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id', $scroll_id, 1);
    } else {
        $Qid_mod = (integer)filter_input(INPUT_POST, 'id_mod');
    }
    // Sobre-escribe el scroll_id que se pueda tener
    if (isset($_POST['stack'])) {
        $stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    } else {
        $stack = '';
    }
    //Si vengo por medio de Posicion, borro la última
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }

    $oModulo = new \devel\model\entity\Modulo($Qid_mod);
    $nom = $oModulo->getNom();
    $descripcion = $oModulo->getDescripcion();
    $mods_req = $oModulo->getMods_req();
    $apps_req = $oModulo->getApps_req();

}

$oModulosConfig = new modulosConfig();

$a_mods_todos = $oModulosConfig->getModsAll();
$a_apps_todas = $oModulosConfig->getAppsAll();

$a_mods_req = [];
$a_apps_mod = [];
if (!empty($mods_req)) {
    $a_mods_req = explode(',', trim($mods_req, '{}'));
}

if (count($a_mods_req) > 0) {
    foreach ($a_mods_req as $id_mod) {
        $a_apps_mod_i = $oModulosConfig->getAppsMods($id_mod);
        $a_apps_mod = array_merge($a_apps_mod, $a_apps_mod_i);
    }
}

$a_apps_req = [];
if (!empty($apps_req)) {
    $a_apps_req = explode(',', trim($apps_req, '{}'));
}


$oHash = new web\Hash();
$campos_chk = 'sel_mods!sel_apps';
$camposForm = 'nom!descripcion!';

$oHash->setcamposForm($camposForm);
$oHash->setcamposNo($campos_chk);
$a_camposHidden = array(
    'campos_chk' => $campos_chk,
    'id_mod' => $Qid_mod,
);
$oHash->setArraycamposHidden($a_camposHidden);

$oHashActualizar = new web\Hash();
$oHashActualizar->setCamposNo('refresh');
$a_camposHiddenActualizar = array(
    'id_mod' => $Qid_mod,
);
$oHashActualizar->setArraycamposHidden($a_camposHiddenActualizar);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashActualizar' => $oHashActualizar,
    'oHash' => $oHash,
    'id_mod' => $Qid_mod,
    'nom' => $nom,
    'descripcion' => $descripcion,
    'mods_req' => $mods_req,
    'apps_req' => $apps_req,
    'a_mods_todos' => $a_mods_todos,
    'a_apps_todas' => $a_apps_todas,
    'a_mods_req' => $a_mods_req,
    'a_apps_req' => $a_apps_req,
    'a_apps_mod' => $a_apps_mod,
    'mod' => $Qmod,
];

$oView = new core\View('devel\controller');
echo $oView->render('modulos_form.phtml', $a_campos);