<?php

use frontend\shared\PostRequest;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/actividadestudios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$oPosicion->recordar();

$Qid_activ = (integer)filter_input(INPUT_POST, 'id_pau');
$Qid_nom = actividadestudios_id_nom_from_sel_post()['id_nom'];

$d = actividadestudios_e43_from_payload(actividadestudios_post_data(PostRequest::getDataFromUrl('/src/actividadestudios/e43_data', [
    'id_nom' => $Qid_nom,
    'id_activ' => $Qid_activ,
])));
$msg_err = $d['msg_err'];
$nom = $d['nom'];
$txt_nacimiento = $d['txt_nacimiento'];
$dl_origen = $d['dl_origen'];
$dl_destino = $d['dl_destino'];
$txt_actividad = $d['txt_actividad'];
$matriculas = $d['matriculas'];
$aAsignaturasMatriculadas = $d['aAsignaturasMatriculadas'];

$oHash = new HashFront();
$oHash->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividadestudios/controller/e43_2_mpdf.php');
$oHash->setCamposForm('id_nom!id_activ');
$h = $oHash->linkSinVal();

if ($msg_err !== '') {
    actividadestudios_echo_string($msg_err . '<br><br>');
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
