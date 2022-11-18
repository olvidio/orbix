<?php

use core\ConfigGlobal;
use usuarios\model\entity\GrupoOUsuario;
use web\Desplegable;
use usuarios\model\PermCtr;
use usuarios\model\entity\PermUsuarioCentro;
use ubis\model\entity\GestorCentroDl;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************
$oPermCtr = new PermCtr();

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_usuario = (integer)strtok($a_sel[0], "#");
    $Qid_item = (string)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
    $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
}

$Qquien = (string)filter_input(INPUT_POST, 'quien');
$Qque = (string)filter_input(INPUT_POST, 'que');

$oUsuario = new GrupoOUsuario(array('id_usuario' => $Qid_usuario)); // La tabla y su heredada
$nombre = $oUsuario->getUsuario();

if (!empty($Qid_item)) {
    $oPermiso = new PermUsuarioCentro(array('id_item' => $Qid_item, 'id_usuario' => $Qid_usuario));
    $id_ctr = $oPermiso->getId_ctr();
    $perm_ctr = $oPermiso->getPerm_ctr();
} else { // es nuevo
    $oPermiso = new PermUsuarioCentro(array('id_usuario' => $Qid_usuario));
    $id_ctr = 0;
    $perm_ctr = 0;
}

//Centros
$GesCentrosDl = new GestorCentroDl();
$oDesplCentros = $GesCentrosDl->getListaCentros('', 'tipo_ctr,nombre_ubi');
$oDesplCentros->setNombre('id_ctr');
$oDesplCentros->setOpcion_sel($id_ctr);
// Permisos
$aOpciones = $oPermCtr->lista_array();
$oDesplAccion = new Desplegable('', $aOpciones, '', false);
$oDesplAccion->setNombre('perm_ctr');
$oDesplAccion->setOpcion_sel($perm_ctr);


$oHash = new web\Hash();
$oHash->setCamposForm('perm_ctr!id_ctr');
$a_camposHidden = array(
    'id_usuario' => $Qid_usuario,
    'id_item' => $Qid_item,
    'que' => 'perm_ctr_update',
    'quien' => $Qquien,
);
$oHash->setArraycamposHidden($a_camposHidden);


$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nombre' => $nombre,
    'oDesplAccion' => $oDesplAccion,
    'oDesplCentros' => $oDesplCentros,
];

$oView = new core\ViewTwig('usuarios/controller');
$oView->renderizar('usuario_perm_ctr.html.twig', $a_campos);
