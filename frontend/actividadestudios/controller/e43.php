<?php

use frontend\shared\PostRequest;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;

// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$msg_err = '';
$Qid_activ = (integer)filter_input(INPUT_POST, 'id_pau');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $parts = explode('#', $a_sel[0]);
    $Qid_nom = (int)($parts[0] ?? 0);
} else {
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
}

$d = PostRequest::getDataFromUrl('/src/actividadestudios/e43_data', [
    'id_nom' => $Qid_nom,
    'id_activ' => $Qid_activ,
]);
$msg_err = $d['msg_err'] ?? '';
$nom = $d['nom'] ?? '';
$txt_nacimiento = $d['txt_nacimiento'] ?? '';
$dl_origen = $d['dl_origen'] ?? '';
$dl_destino = $d['dl_destino'] ?? '';
$txt_actividad = $d['txt_actividad'] ?? '';
$matriculas = (int)($d['matriculas'] ?? 0);
$aAsignaturasMatriculadas = $d['aAsignaturasMatriculadas'] ?? [];

$oHash = new HashFront();
$oHash->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividadestudios/controller/e43_2_mpdf.php');
$oHash->setCamposForm('id_nom!id_activ');
$h = $oHash->linkSinVal();


if (!empty($msg_err)) {
    echo $msg_err . "<br><br>";
}

$a_campos = ['oPosicion' => $oPosicion,
    'id_nom' => $Qid_nom,
    'h' => $h,
    'id_activ' => $Qid_activ,
    'dl_destino' => $dl_destino,
    'dl_origen' => $dl_origen,
    'nom' => $nom,
    'txt_nacimiento' => $txt_nacimiento,
    'txt_actividad' => $txt_actividad,
    'matriculas' => $matriculas,
    'aAsignaturasMatriculadas' => $aAsignaturasMatriculadas,
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('e43.phtml', $a_campos);
